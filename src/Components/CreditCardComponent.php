<?php

namespace App\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
	name: 'credit-card',
	template: 'custom/components/credit-card.html.twig'
)]
class CreditCardComponent
{
}