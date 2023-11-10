<?php
namespace EaglenavigatorSystem\Wopi\Interfaces;


interface Deletaable
{
    /**
     * Delete the document
     *
     * @return void
     */
    public function delete(): void;

    /**
     * indicates if the document is able to be deleted
     *
     * @return bool
     */
     public function isDeleteable(): bool;
}
