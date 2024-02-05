<?php

// src/Components/TablesComponent.php
namespace App\Components;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
	name: 'datatable-basic',
	template: 'custom/tables/ui-datatable-basic.html.twig'
)]
class UiDatatableComponent
{
	public string $tableTitle;
	public string $cardTitle;
	public array $datas;
	public array $columnsName;
	public string $addButton;
	public array $actions;
}