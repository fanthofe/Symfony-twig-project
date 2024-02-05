<?php

namespace App\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
	name: 'list-checkbox-event',
	template: 'custom/components/list-checkbox-event.html.twig'
)]
class ListCheckboxEventComponent
{
}