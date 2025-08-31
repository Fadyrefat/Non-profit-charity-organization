<?php
interface Observer {
    public function update(int $requestId, string $type, int $number): void;
}
