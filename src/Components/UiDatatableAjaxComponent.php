<?php

// src/Components/TablesComponent.php
namespace App\Components;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
	name: 'datatable-ajax',
	template: 'custom/tables/ui-datatable-ajax.html.twig'
)]
class UiDatatableAjaxComponent
{
	public string $tableTitle;
	public string $cardTitle;
	public array $columnsName;
	public array $columnsFrName;
	public string $ajaxLink;
	public string $addButton;
}