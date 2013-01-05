<?php

/*
 * This file is part of the GremoHighchartsBundle package.
 *
 * (c) Marco Polichetti <gremo1982@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gremo\HighchartsBundle\Provider;

use Gremo\HighchartsBundle\Common\Exception\UnexpectedTypeException;

class ChainOptionsProvider implements OptionsProviderInterface
{
    /**
     * @var array|OptionsProviderInterface[]
     */
    private $providers;

    /**
     * Creates a new options provider chain.
     */
    public function __construct()
    {
        $this->providers = array();
    }

    /**
     * Register a new options provider with the (optional) given priority
     *
     * @param OptionsProviderInterface $provider
     * @param int $priority
     */
    public function addOptionsProvider(OptionsProviderInterface $provider, $priority = 0)
    {
        if(!isset($this->providers[$priority])) {
            $this->providers[$priority] = array();
        }

        $this->providers[$priority][] = $provider;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        // Return empty options if there are no providers
        if(empty($this->providers)) {
            return array();
        }

        // Sort and merge providers
        ksort($this->providers);
        $providers = call_user_func_array('array_merge', $this->providers);

        $options = array();
        foreach($providers as $provider) {
            $providerOptions = $provider->getOptions();

            if(!is_array($providerOptions)) {
                throw new UnexpectedTypeException('array', $providerOptions);
            }

            $options = array_replace_recursive($options, $providerOptions);
        }

        return $options;
    }
}
