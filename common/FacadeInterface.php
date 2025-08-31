<?php

interface FacadeInterface
{
    public function handleRequest($action, $method, $data = []);
}
