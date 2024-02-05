<?php

namespace App\Components;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
	name: 'form-modal-update',
	template: 'custom/forms/form-modal-update-entity.html.twig'
)]
class FormModalUpdateEntityComponent
{
	public string $entityName;
	public object $entity;
	public string $entityId;
	public object $form;
}