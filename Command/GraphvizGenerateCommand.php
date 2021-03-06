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
use Symfony\Component\Console\Output\OutputInterface;

/**
 * GraphvizGenerateCommand.
 *
 * @author Duncan de Boer <duncan@charpand.nl>
 */
class GraphvizGenerateCommand extends AbstractCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setDescription('Generates Graphviz file for your project')
            ->setHelp(<<<EOT
The <info>%command.name%</info> generates Graphviz file for your project.

  <info>php %command.full_name%</info>
EOT
            )
            ->setName('propel:graphviz:generate');
    }

    /**
     * @throws \InvalidArgumentException When the target directory does not exist
     * @see Command
     *
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dest = $this->getApplication()->getKernel()->getRootDir() . '/propel/graph/';

        $this->callPhing('graphviz', [
            'propel.graph.dir' => $dest,
        ]);

        $this->writeNewDirectory($output, $dest);
    }
}
