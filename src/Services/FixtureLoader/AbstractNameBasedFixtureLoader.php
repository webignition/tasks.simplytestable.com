<?php

namespace App\Services\FixtureLoader;

use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractNameBasedFixtureLoader extends AbstractFixtureLoader implements FixtureLoaderInterface
{
    abstract protected function createEntity(string $name);

    public function load(?OutputInterface $output = null): void
    {
        $entityClass = $this->getEntityClass();

        if ($output) {
            $output->writeln('Loading fixtures for ' . $entityClass . ' ...');
        }

        foreach ($this->data as $name) {
            if ($output) {
                $output->write("  " . '<comment>' . $name . '</comment> ...');
            }

            $entity = $this->repository->findOneBy([
                'name' => $name,
            ]);

            if (!$entity) {
                if ($output) {
                    $output->write(' <fg=cyan>creating</>');
                }

                $entity = $this->createEntity($name);
                $this->entityManager->persist($entity);
                $this->entityManager->flush();
            }

            if ($output) {
                $output->writeln(' <info>✓</info>');
            }
        }

        if ($output) {
            $output->writeln('');
        }
    }
}
