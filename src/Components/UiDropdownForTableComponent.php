<?php

// src/Components/TablesComponent.php
namespace App\Components;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
	name: 'ui-dropdown',
	template: 'custom/basicUi/ui-dropdown-table.html.twig'
)]
class UiDropdownForTableComponent
{
	public string $dropdownTitle;
	public array $dropdownItems;
	public string $dataId;
}