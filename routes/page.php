<?php

declare(strict_types=1);

use Robs\Component\App\Components\Button;
use Robs\Component\App\Components\Form;
use Robs\Component\App\Components\InputGroup;
use Robs\Component\App\Plugin\Url;
use Robs\Component\Router\Reactive;

return #[Reactive] function () {
    yield new Form(
        action: new Url('/'),
        children: [
            new InputGroup('text', 'name', 'Enter your name to be greeted: '),
            new Button('submit', 'Submit')
        ],
    );
};
