<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Security
 * @copyright   Copyright (c) 2018 Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Security\Console\Command;

use Magento\Framework\App\Config\Storage\Writer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ListReset
 * @package Mageplaza\Security\Console\Command
 */
class ListReset extends Command
{
    /**
     * @var Writer
     */
    protected $_writer;

    /**
     * ListReset constructor.
     * @param Writer $writer
     * @param null $name
     */
    public function __construct(
        Writer $writer,
        $name = null
    )
    {
        $this->_writer = $writer;
        parent::__construct($name);
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('security:reset')
            ->setDescription('Reset Security Black-Whitelist')
            ->setDefinition([
                new InputArgument(
                    'value',
                    InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
                    'Space-separated list of index types or omit to apply to all indexes.'
                )
            ]);

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getArgument('value')) {
            $requestedTypes = $input->getArgument('value');
            $requestedTypes = array_filter(array_map('trim', $requestedTypes), 'strlen');
        }

        if (empty($requestedTypes)) {
            try {
                $this->reset('black_list');
                $this->reset('white_list');
                $output->writeln('<info>Blacklist And WhiteList Reset Successfully!</info>');
            } catch (\Exception $e) {
                $output->writeln("<error>{$e->getMessage()}</error>");
            }

            return;
        }

        foreach ($requestedTypes as $item) {
            if ($item == 'whitelist') {
                try {
                    $this->reset('white_list');
                    $output->writeln('<info>Whitelist Reset Successfully!</info>');
                } catch (\Exception $e) {
                    $output->writeln("<error>{$e->getMessage()}</error>");
                }
            } else if ($item == 'blacklist') {
                try {
                    $this->reset('black_list');
                    $output->writeln('<info>Blacklist Reset Successfully!</info>');
                } catch (\Exception $e) {
                    $output->writeln("<error>{$e->getMessage()}</error>");
                }
            } else {
                $output->writeln("<error>Wrong value '" . $item . "'</error>");
            }
        }
    }

    /**
     * @param $list
     */
    private function reset($list)
    {
        $this->_writer->save(
            'security/black_white_list/' . $list,
            '',
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            \Magento\Store\Model\Store::DEFAULT_STORE_ID
        );
    }
}
