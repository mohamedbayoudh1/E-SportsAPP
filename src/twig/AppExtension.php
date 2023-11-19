<?php

// src/Twig/AppExtension.php

namespace App\twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use App\Repository\NotifRepository;

class AppExtension extends AbstractExtension
{
private $notifRepository;

public function __construct(NotifRepository $notifRepository)
{
$this->notifRepository = $notifRepository;
}

public function getFunctions()
{
return [
new TwigFunction('findNotifs', [$this, 'findNotifs']),
];
}

public function findNotifs()
{
return $this->notifRepository->findAll();
}
}
