<?php

namespace game;

use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\CommandExecutor;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\item\Item;
use pocketmine\Inventory;
use onebone\economyapi\EconomyAPI;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\level\Level;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\utils\TextFormat;
use jojoe77777\FormAPI;

class Main extends PluginBase implements Listener
{

  public $answer;
  public $work;
  public $data;
  public $i;
  public $c;

  public function onEnable()
  {
    @mkdir($this->getDataFolder());
    $this->saveResource("commands.yml");
    $this->getCommandsConfig = new Config($this->getDataFolder() . "commands.yml", Config::YAML);
    $commandsConfig = $this->getCommandsConfig()->getAll();
    foreach ($commandsConfig["Commands"] as $var) {
      $this->getScheduler()->scheduleRepeatingTask(new runCommand($this, $var["Command"]), $var["Time"]);
      $this->eco = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
      $this->getServer()->getPluginManager()->registerEvents($this, $this);
      $this->saveDefaultConfig();
      # $var as I do not know what to call it...
      # $var["Command"] calls the user inputted commands, and  $var["Time"] is the user inputted times.
    }
  }

  public function onCommand(CommandSender $sender, Command $command, String $label, array $args): bool
  {
    if ($command->getName() == "game") {
      $this->Formgame($sender);
      return true;
    }
  }

  public function Formgame(Player $sender)
  {
    $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
    $form = $api->createSimpleForm(function (Player $sender,  int $data = null) {
      $result = $data;
      if ($result === null) {
        return true;
      }
      switch ($result) {
        case "0";
          $name = $sender->getName();
          $Game = ('Game');
          $kdata = new Config($this->getDataFolder() . "data/" . strtolower($Game) . ".yml", Config::YAML);
          if ($kdata->get("$name")) {
            $sender->sendMessage("§l§4!§0[§6คะแนน§0]§4! §e§lคะแนนสะสมของคุณ§6 " . $kdata->get("$name") . " §bคะแนน§r");
          } else {
            $sender->sendMessage("§l§4!§0[§6คะแนน§0]§4!§f ผู้เล่นท่านนี้ไม่มีคะแนน§r");
          }
          break;

        case "1";
          $this->Formyou($sender);
          break;

        case "2";
          $name = $sender->getName();
          $Game = ('Game');
          $kdata = new Config($this->getDataFolder() . "data/" . strtolower($Game) . ".yml", Config::YAML);
          $bbm = (100);
          $ggmc = $kdata->get("$name");
          if ($ggmc >= $bbm) {
            $giftrand = (mt_rand(1000000, 3000000));
            $this->eco = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
            $this->eco->addMoney($sender->getName(), $giftrand);
            $kdata->exists("$name");
            $kdata->set("$name", $kdata->get("$name") - $bbm);
            $kdata->save();
            $sender->sendMessage("§l§4!§0[§6คะแนน§0]§4!§f สุ่มได้รับ§6 $giftrand §r");
          } else {
            $sender->sendMessage("§l§4!§0[§6คะแนน§0]§4!§f คะแนนของคุณไม่เพียงพอที่จะแลก§r");
          }
          break;

        case "3";
          $name = $sender->getName();
          $Game = ('Game');
          $kdata = new Config($this->getDataFolder() . "data/" . strtolower($Game) . ".yml", Config::YAML);
          $bbm = (500);
          $ggmc = $kdata->get("$name");
          if ($ggmc >= $bbm) {
            $giftrand = (mt_rand(1, 5));
            $itemok1 = Item::get(397, $giftrand, 1);
            $sender->getInventory()->addItem($itemok1);
            $kdata->exists("$name");
            $kdata->set("$name", $kdata->get("$name") - $bbm);
            $kdata->save();
            $sender->sendMessage("§l§4!§0[§6คะแนน§0]§4!§f แลกของให้เรียบร้อย§r");
          } else {
            $sender->sendMessage("§l§4!§0[§6คะแนน§0]§4!§f คะแนนของคุณไม่เพียงพอที่จะแลก§r");
          }
          break;

        case "4";
          $name = $sender->getName();
          $Game = ('Game');
          $kdata = new Config($this->getDataFolder() . "data/" . strtolower($Game) . ".yml", Config::YAML);
          $swallet = $kdata->getAll();
          $c = count($swallet);
          $message = "";
          $top = "§l§4〤§b[§5ˇωˇ§b]§r§6 Game§f เช็คอันดับคะแนน §l§b[§5ˇωˇ§b]§4〤§r";
          arsort($swallet);
          $i = 1;
          foreach ($swallet as $name => $amount) {

            $message .= "§b " . $i . ". §7" . $name . "  §cＧ§6a§em§ae  §f" . $amount . " §aคะแนน§r\n";
            if ($i > 14) {
              break;
            }
            ++$i;
          }

          $sender->sendMessage("$top\n$message");
          break;
      }
    });
    $form->setTitle("§l§aตรวจสอบคะแนนตอบคำถาม");
    $form->setContent("§l§fเลือกหัวข้อที่ต้องการ\n\n");
    $form->addButton("§l§fตรวจสอบ§bคะแนน§aของฉัน\n", 0, "textures/ui/Friend1");
    $form->addButton("§l§fเช็คคะ§bแนนของ§aเพื่อน", 0, "textures/ui/FriendsDiversity");
    $form->addButton("§l§fสุ่ม§eเงิน §bล้าน §6100 §fคะแนน", 0, "textures/ui/gift_square");
    $form->addButton("§bสุ่มหัว §fต่างๆ §6500 §fคะแนน", 0, "textures/ui/gift_square");
    $form->addButton("§l§fเช็คคะแนน§e Top§6 15§r");
    $form->sendToPlayer($sender);
    return $form;
  }


  public function Formyou(Player $sender)
  {
    $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
    $form = $api->createCustomForm(function (Player $sender, $data) {
      $gameyou = $data[1];
      if ($data !== null) {
        $Game = ('Game');
        $kdata = new Config($this->getDataFolder() . "data/" . strtolower($Game) . ".yml", Config::YAML);
        if ($kdata->get("$data[1]")) {
          $sender->sendMessage("§l§4!§0[§6คะแนน§0]§4! §e§lคะแนนสะสมของคุณ§a $gameyou §6 " . $kdata->get("$data[1]") . " §bคะแนน§r");
        } else {
          $sender->sendMessage("§l§4!§0[§6คะแนน§0]§4!§f ผู้เล่นท่านนี้ไม่มีคะแนน§r");
        }
      }
    });
    $form->setTitle("§l§aตรวจสอบคะแนนตอบคำถาม");
    $form->addLabel("§f§lกรอกชื่อคนที่ท่านจะดูคะแนน §c-§fชื่อเต็ม§c-§r\n\n");
    $form->addInput("§l§fกรองชื่อ §c!§bเต็ม§c!§r");
    $form->sendToPlayer($sender);
  }

  public function executeCommand($command)
  {
    @mkdir($this->getDataFolder());
    @mkdir($this->getDataFolder() . "data/");
    $max = $this->getConfig()->get("max");
    $min = $this->getConfig()->get("min");
    $characters = ('0123456789abcdefghijklmnopqrstuvxyz');
    $charactersLength = strlen($characters);
    $length = (5);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    $rand = (mt_rand(1, 1));
    $randdd = (1);
    switch ($rand) {

      case 1:
        if ($this->getConfig()->get("item") == false) {
          $this->getServer()->broadcastMessage("§l§4!§0[§6Game§0]§4! §fจงพิมคำต่อไปนี้§6 $randomString");
        }
        if ($this->getConfig()->get("item") == true) {
        }
        $this->answer = $randomString;
        $this->work = $randdd;
        break;
    }
  }
  public function onPlayerChatEvent(PlayerChatEvent $event)
  {
    $playere = $event->getPlayer();
    $name = $playere->getName();
    $message = $event->getMessage();
    $randdd = (1);
    $random = mt_rand($this->getConfig()->get("min"), $this->getConfig()->get("max"));
    if ($this->work == $randdd) {
      if (strtolower($message) == $this->answer) {
        if ($this->getConfig()->get("item") == false) {
          $Game = ('Game');
          $kdata = new Config($this->getDataFolder() . "data/" . strtolower($Game) . ".yml", Config::YAML);
          $kdata->exists("$name");
          $kdata->set("$name", $kdata->get("$name") + 1);
          $kdata->save();
          $rand6 = (mt_rand(1, 2));
          $playere->sendMessage("§l§4!§0[§6Game§0]§4!§f คุณตอบถูก รับเงินรางวัล§b $random §fบาท§r");
          $this->getServer()->broadcastMessage("§l§4!§0[§6Game§0]§4! §fผู้เล่น§a " . $playere->getName() . " §fตอบคำถามถูก คะแนนสะสม§6 " . $kdata->get("$name") . " §fคะแนน§r");
          $playere->addTitle("§b§lคะแนนสะสม§6 " . $kdata->get("$name") . " §bคะแนน§r");
          if ($this->getServer()->getPluginManager()->getPlugin("EconomyAPI") != null) {
            $this->eco = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
            $this->eco->addMoney($playere->getName(), $random);
          }
          if ($this->getServer()->getPluginManager()->getPlugin("EconomyPlus") != null) {
            $mi = EconomyPlus::getInstance()->addMoney($playere->getName(), $random);
            $mi;
          }
        }
        if ($this->getConfig()->get("item") == true) {
          //  $randomi = mt_rand(264, 303, 261, 32, 350, 354, 265, 266);
          $amount = mt_rand(1, 5);
          $sender = $playere;
          switch (mt_rand(1, 5)) {
            case 1:

              $playere->getInventory()->addItem(Item::get(ITEM::DIAMOND, 0, 1, "เพรข"));
              $sender->sendMessage("§7(§aเกมส์ตอบคำถาม§7) แลกไอเทมเเล้ว");

              break;
            case 2:

              $playere->getInventory()->addItem(Item::get(ITEM::IRON_BOOTS, 0, 1, "รองเท้าเหล็ก"));
              $sender->sendMessage("§7(§aเกมส์ตอบคำถาม§7) แลกไอเทมเเล้ว");
              break;

            case 3:

              $playere->getInventory()->addItem(Item::get(ITEM::STEAK, 0, 2, "เนื้อวัว"));
              $sender->sendMessage("§7(§aเกมส์ตอบคำถาม§7) แลกไอเทมเเล้ว");
              break;

            case 4:

              $playere->getInventory()->addItem(Item::get(ITEM::EMERALD, 0, 1, "มรกต"));
              $sender->sendMessage("§7(§aเกมส์ตอบคำถาม§7) แลกไอเทมเเล้ว");
              break;

            case 5:

              $playere->getInventory()->addItem(Item::get(ITEM::EMERALD, 0, 1, "มรกต"));
              $sender->sendMessage("§7(§aเกมส์ตอบคำถาม§7) แลกไอเทมเเล้ว");
              break;
          }
        }
        $this->work = 0;
        $event->setCancelled(true);
      }
    }
  }

  public function PlayerChatEvent($name)
  {
    $Game = ('Game');
    $data = new Config($this->getDataFolder() . "data/" . strtolower($Game) . ".yml", Config::YAML);
    //Check data
    $data->exists("$name") && $data->exists("deaths");
    return $data->get("$name");
    $data->setAll(array("$name" => 0, "deaths" => 0));
    $data->save();
  }


  public function getCommandsConfig()
  {
    return $this->getCommandsConfig;
  }
}
