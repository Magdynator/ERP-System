<?php

declare(strict_types=1);

namespace Erp\Core\Contracts;

interface RepositoryInterface
{
    public function find(int $id): ?object;

    public function all(): iterable;

    public function create(array $data): object;

    public function update(object $model, array $data): object;

    public function delete(object $model): bool;
}
