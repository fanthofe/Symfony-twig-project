<?php

namespace App\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
	name: 'list-date-event',
	template: 'custom/components/list-date-event.html.twig'
)]
class ListDateEventComponent
{
	public array $users = [];
	public array $events = [];
	public string $nbAllEvents = '';
}