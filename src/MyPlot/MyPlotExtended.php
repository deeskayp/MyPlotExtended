<?php
declare(strict_types=1);

namespace MyPlot;

use pocketmine\event\level\LevelLoadEvent;
use onebone\economyapi\EconomyAPI;
use EssentialsPE\Loader;

class MyPlotExtended extends MyPlot {
  public function onEnable() : void {

    // Initialize EconomyProvider
    if ($this->getConfig()->get('UseEconomy', false) === true) {
      if (($plugin = $this->getServer()->getPluginManager()->getPlugin('EconomyAPI')) !== null) {
        if ($plugin instanceof EconomyAPI) {
          $this->economyProvider = new EconomySProvider($plugin);
          $this->getLogger()->debug('Eco set to EconomySProvider');
        }
        else {
          $this->getLogger()->debug("Eco not instance of EconomyAPI");
        }
      }
      elseif (($plugin = $this->getServer()->getPluginManager()->getPlugin("EssentialsPE")) !== null) {
        if($plugin instanceof Loader) {
          $this->economyProvider = new EssentialsPEProvider($plugin);
          $this->getLogger()->debug("Eco set to EssentialsPE");
        }
        else {
          $this->getLogger()->debug("Eco not instance of EssentialsPE");
        }
      }
      elseif (($plugin = $this->getServer()->getPluginManager()->getPlugin("PocketMoney")) !== null) {
        if ($plugin instanceof PocketMoney) {
          $this->economyProvider = new PocketMoneyProvider($plugin);
          $this->getLogger()->debug("Eco set to PocketMoney");
        }
        else {
          $this->getLogger()->debug("Eco not instance of PocketMoney");
        }
      }
      if (!isset($this->economyProvider)) {
        $this->getLogger()->info("No supported economy plugin found!");
        $this->getConfig()->set("UseEconomy", false);
      }
    }
    $this->getLogger()->debug(TF::BOLD . "Loading Events");

    $eventListener = new EventListenerExtended($this);
    $this->getServer()->getPluginManager()->registerEvents($eventListener, $this);
    $this->getLogger()->debug(TF::BOLD . "Registering Loaded Levels");
    foreach ($this->getServer()->getLevels() as $level) {
      $eventListener->onLevelLoad(new LevelLoadEvent($level));
    }
    $this->getLogger()->debug(TF::BOLD.TF::GREEN."Enabled!");
  }
}
