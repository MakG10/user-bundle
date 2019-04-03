<?php


namespace MakG\UserBundle\AvatarGenerator;


use MakG\UserBundle\Exception\MissingDependencyException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IdenticonGenerator implements AvatarGeneratorInterface
{
    public function generate(?string $salt, array $options = []): string
    {
        if ( ! class_exists('\Identicon\Identicon')) {
            throw new MissingDependencyException('yzalis/identicon package is required to use this avatar generator.');
        }

        $options   = $this->resolveOptions($options);
        $identicon = new \Identicon\Identicon();

        return $identicon->getImageData($salt, $options['size'], $options['color'], $options['backgroundColor']);
    }

    private function resolveOptions(array $options)
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults(
            [
                'size'            => 128,
                'color'           => null,
                'backgroundColor' => null,
            ]
        );

        return $resolver->resolve($options);
    }
}