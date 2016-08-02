<?php namespace Felixkiss\MultipleValidators;

use Closure;
use Symfony\Component\Translation\TranslatorInterface;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Validation\Factory as FactoryContract;
use Illuminate\Validation\Factory as BaseFactory;

class Factory extends BaseFactory
{
    /**
     * @var \Closure[]
     */
    private $resolvers;

    /**
     * @var \Illuminate\Contracts\Validation\Factory[]
     */
    private $factories;

    /**
     * Create a new Validator factory instance.
     *
     * @param  \Symfony\Component\Translation\TranslatorInterface  $translator
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @return void
     */
    public function __construct(TranslatorInterface $translator, Container $container = null)
    {
        $this->container = $container;
        $this->translator = $translator;

        $this->resolvers = [];
        $this->factories = [];
    }

    /**
     * Add a validation factory.
     *
     * @param  \Illuminate\Contracts\Validation\Factory
     * @return void
     */
    public function addFactory(FactoryContract $factory)
    {
        $this->factories[] = $factory;
    }

    /**
     * Add a Validator instance resolver.
     *
     * @param  \Closure  $resolver
     * @return void
     */
    public function resolver(Closure $resolver)
    {
        $this->resolvers[] = $resolver;
    }

    /**
     * Resolve a new Validator instance.
     *
     * @param  array  $data
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $customAttributes
     * @return \Illuminate\Validation\Validator
     */
    protected function resolve(array $data, array $rules, array $messages, array $customAttributes)
    {
        $proxy = new Validator($this->translator, $data, $rules, $messages, $customAttributes);
        $proxy->setPresenceVerifier($this->getPresenceVerifier());

        foreach ($this->factories as $factory) {
            $validator = $factory->resolve($data, $rules, $messages, $customAttributes);
            $proxy->addValidator($validator);
        }

        foreach ($this->resolvers as $resolver) {
            $validator = $resolver($this->translator, $data, $rules, $messages, $customAttributes);
            $proxy->addValidator($validator);
        }

        return $proxy;
    }
}
