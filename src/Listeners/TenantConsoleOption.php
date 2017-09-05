<?php

namespace Elimuswift\Tenancy\Listeners;

use Illuminate\Foundation\Application;
use Elimuswift\Tenancy\Traits\DispatchesEvents;
use Illuminate\Contracts\Events\Dispatcher;
use Symfony\Component\Console\ConsoleEvents;
use Elimuswift\Tenancy\Events\Hostnames\Identified;
use Illuminate\Console\Events\ArtisanStarting;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleExceptionEvent;
use Elimuswift\Tenancy\Contracts\Repositories\WebsiteRepository as Site;

class TenantConsoleOption
{
    /**
     * Laravel Application.
     *
     * @var Application
     **/
    protected $app;
    /**
     * WebsiteRepository.
     *
     * @var WebsiteRepository
     **/
    protected $website;

    use DispatchesEvents;

    public function __construct(Application $app, Site $website)
    {
        $this->website = $website;
        $this->app = $app;
    }

    /**
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(ArtisanStarting::class, [$this, 'addOption']);
    }

    /**
     * @param ArtisanStarting $event
     */
    public function addOption(ArtisanStarting $event)
    {
        $definition = $event->artisan->getDefinition();
        $definition->addOption(new InputOption('--tenant', null, InputOption::VALUE_OPTIONAL, 'The website the command should be run for (id,uuid).'));
        $event->artisan->setDefinition($definition);
        $dispatcher = $this->dispatcher();
        $dispatcher->addListener(ConsoleEvents::COMMAND, function (ConsoleCommandEvent $event) {
            $tenant = $event->getInput()->getParameterOption('--tenant', '');
            if ($tenant) {
                $command = $event->getCommand();
                $input = $event->getInput();
                $output = $event->getOutput();
                if ($tenant == '*' || $tenant == 'all') {
                    $websites = $this->website->getAll();
                    foreach ($websites as $website) {
                        // Fire tenant identification Event
                        $host = $website->hostnames->first();
                        $this->emitEvent(new Identified($host));
                        $output->writeln('<info>Running command for ' . $website->uuid . '</info>');
                        try {
                            $command->run($input, $output);
                        } catch (\Exception $e) {
                            $event = new ConsoleExceptionEvent($command, $input, $output, $e, $e->getCode());
                            $this->dispatcher()->dispatch(ConsoleEvents::EXCEPTION, $event);

                            throw $event->getException();
                        }
                    }
                } elseif ($website = $this->website->findByUuidOrId($tenant)) {
                    $output->writeln('<info>Running command for ' . $website->uuid . '</info>');
                    $host = $website->hostnames->first();
                    $this->emitEvent(new Identified($host));

                    try {
                        $command->run($input, $output);
                    } catch (\Exception $e) {
                        $event = new ConsoleExceptionEvent($command, $input, $output, $e, $e->getCode());
                        $this->dispatcher()->dispatch(ConsoleEvents::EXCEPTION, $event);

                        throw $event->getException();
                    }
                } else {
                    $output->writeln('<error>Failed to resolve tenant</error>');
                }
                $event->disableCommand();
            }
        });
        $event->artisan->setDispatcher($dispatcher);
    }

    /**
     * Symfony event dispatcher.
     *
     * @return \Symfony\Component\EventDispatcher\EventDispatcher;
     **/
    protected function dispatcher()
    {
        return app(EventDispatcher::class);
    }
}
