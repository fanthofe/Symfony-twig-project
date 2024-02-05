<?php

namespace App\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
	name: 'fullcalendar',
	template: 'custom/components/fullcalendar.html.twig'
)]
class FullcalendarComponent
{
	public array $events;
}