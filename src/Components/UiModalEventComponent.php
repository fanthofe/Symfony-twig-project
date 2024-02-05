<?php

namespace App\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
	name: 'ui-modal',
	template: 'custom/basicUi/ui-modal.html.twig'
)]
class UiModalEventComponent
{

}