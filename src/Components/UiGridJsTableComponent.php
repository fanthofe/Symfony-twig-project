<?php

// src/Components/TablesComponent.php
namespace App\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'custom/tables/ui-gridjs-table.html.twig')]
class UiGridJsTableComponent
{
	public string $cardTitle;
	public string $tableTitle;
	public array $columnsName;
	public string $ajaxLink;
	public string $addButton;
}