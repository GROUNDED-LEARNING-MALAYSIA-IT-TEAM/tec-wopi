<?php

namespace EaglenavigatorSystem\Wopi\Interfaces;

interface CheckFileInfoInterface
{
    /**
     * Check File Info
     *
     * @param  string              $fileId
     * @return \Cake\Http\Response
     */
    public function index(string $fileId);
}
