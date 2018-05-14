<?php
namespace Comolo\SuperLoginClient\ContaoEdition\User;

interface RemoteUserInterface
{
    public function toArray();
    public function validate();
    public function getUsername();
    public function setId($id);
    public function getId();
}
