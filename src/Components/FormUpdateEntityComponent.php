<?php

// src/Components/TablesComponent.php
namespace App\Components;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
	name: 'form-update',
	template: 'custom/forms/form-update-entity.html.twig'
)]
class FormUpdateEntityComponent
{
	public string $entityName;
	public object $entity;
	public string $entityId;
	public object $form;
	public string $typeForm;
}