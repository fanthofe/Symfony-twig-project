<?php

// src/Components/TablesComponent.php
namespace App\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
	name: 'ui-alert',
	template: 'custom/basicUi/ui-alert.html.twig'
)]
class UiAlertComponent
{
	public string $type = 'success';

	public function mount(bool $isSuccess = true)
	{
		$this->type = $isSuccess ? 'success' : 'danger';
	}
}