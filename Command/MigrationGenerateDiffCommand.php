<?php

/**
 * This file is part of the PropelBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Propel\Bundle\PropelBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * MigrationGenerateDiffCommand.
 *
 * @author Duncan de Boer <duncan@charpand.nl>
 */
class MigrationGenerateDiffCommand extends AbstractCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setDescription('Generates SQL diff between the XML schemas and the current database structure')
            ->addOption('connection', null, InputOption::VALUE_OPTIONAL, 'Set this parameter to define a connection to use')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command compares the current database structure and the available schemas. If there is a difference, it creates a migration file.

  <info>php %command.full_name%</info>
EOT
            )
            ->setName('propel:migration:generate-diff');
    }

    /**
     * @throws \InvalidArgumentException When the target directory does not exist
     * @see Command
     *
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (true === $this->callPhing('diff')) {
            $this->writeSummary($output, 'propel-sql-diff');
        } elseif (strpos($this->buffer, 'Uncommitted migrations have been found')) {
            $this->writeSection($output, [
                '[Propel] Error',
                '',
                'Uncommitted migrations have been found. You should either execute or delete them before rerunning the propel:migration:generate-diff command.'
            ], 'fg=white;bg=red');
        } else {
            $this->writeTaskError($output, 'propel-sql-diff');
        }
    }
}
