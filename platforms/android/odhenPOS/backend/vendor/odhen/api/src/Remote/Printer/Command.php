<?php

namespace Odhen\API\Remote\Printer;

class Command
{

    protected $commands = array();

    public function addCommand($command, array $parameters = array())
    {
        $this->commands[] = array(
            'name' => $command,
            'parameters' => $parameters
        );
        return $this;
    }

    protected function getParameter($options, $parameter, $defaultValue = null)
    {
        return isset($options[$parameter]) ? $options[$parameter] : $defaultValue;
    }

    public function text($text, array $options = array())
    {
        return $this->addCommand('text', array(
            'text'       => $text,
            'italic'     => $this->getParameter($options, 'italic')     ? 1 : 0,
            'underlined' => $this->getParameter($options, 'underlined') ? 1 : 0,
            'expanded'   => $this->getParameter($options, 'expanded')   ? 1 : 0,
            'bold'       => $this->getParameter($options, 'bold')       ? 1 : 0,
            'letterType' => $this->getParameter($options, 'letterType', 1)
        ));
    }

    public function cutPaper()
    {
        return $this->addCommand('cutPaper');
    }

    public function barCode($text, array $options = array())
    {
        return $this->addCommand('barCode', array(
            'text'     => $text,
            'height'   => $this->getParameter($options, 'height'),
            'width'    => $this->getParameter($options, 'width'),
            'position' => $this->getParameter($options, 'position'),
            'font'     => $this->getParameter($options, 'font'),
            'margin'   => $this->getParameter($options, 'margin')
        ));
    }

    public function qrCode($text, array $options = array())
    {
        return $this->addCommand('qrCode', array(
            'text'     => $text,
            'height'   => $this->getParameter($options, 'height'),
            'width'    => $this->getParameter($options, 'width'),
            'position' => $this->getParameter($options, 'position'),
            'font'     => $this->getParameter($options, 'font'),
            'margin'   => $this->getParameter($options, 'margin')
        ));
    }

    public function comandoTx($command)
    {
        return $this->addCommand('commandTx', array(
            'commandName'       => $command,
        ));
    }

    public function getCommands()
    {
        return $this->commands;
    }
}
