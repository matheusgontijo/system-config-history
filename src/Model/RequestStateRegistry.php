<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Model;

use Symfony\Component\HttpFoundation\Request;

class RequestStateRegistry
{
    private ?Request $request = null;

    public function setRequest(Request $request): void
    {
        if ($this->request !== null) {
            return;
        }

        $this->request = $request;
    }

    public function getRequest(): ?Request
    {
        return $this->request;
    }
}
