<?php
interface ObserverInterface
{
    public function update($event, $payload);
}
