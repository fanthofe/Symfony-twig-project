<?php

namespace App\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
	name: 'ui-modal-stimulus',
	template: 'custom/basicUi/ui-modal-stimulus.html.twig'
)]
class UiModalStimulusEventComponent
{
}