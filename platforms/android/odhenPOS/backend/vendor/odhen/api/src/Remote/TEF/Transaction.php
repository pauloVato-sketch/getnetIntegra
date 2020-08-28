<?php
namespace Odhen\API\Remote\TEF;

class Transaction
{
  protected $endSitef;
  protected $storeId;
  protected $terminalId;

  public function __construct()
  { }

  public function setEndSitef($endSitef)
  {
    $this->endSitef = $endSitef;
  }
  public function setStoreId($storeId)
  {
    $this->storeId = $storeId;
  }
  public function setTerminalId($terminalId)
  {
    $this->terminalId = $terminalId;
  }
  public function getEndSitef()
  {
    return $this->endSitef;
  }
  public function getStoreId()
  {
    return $this->storeId;
  }
  public function getTerminalId()
  {
    return $this->terminalId;
  }
}
