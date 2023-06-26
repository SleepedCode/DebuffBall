<?php

namespace sleepedcode;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\VanillaItems;
use pocketmine\plugin\PluginBase;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class DebuffBall extends PluginBase implements Listener {

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onEntityDamageByEntity(EntityDamageByEntityEvent $event): void {
        $damager = $event->getDamager();
        $entity = $event->getEntity();

        if ($damager instanceof Player && $entity instanceof Player) {
            if ($damager->getInventory()->getItemInHand()->getTypeId() === ItemTypeIds::SLIMEBALL) {
                if (self::isDebuffBall($damager->getInventory()->getItemInHand())) {
                    $entity->getEffects()->add(new EffectInstance(VanillaEffects::BLINDNESS(), 4 * 4, 2));
                    $entity->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), 4 * 4, 2));
                    $entity->sendMessage("¡Has sido afectado por un DebuffBall!");
                }
            }
        }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if ($command->getName() === "giveball") {
            if (isset($args[0])) {
                $player = $this->getServer()->getPlayerByPrefix($args[0]);
                if ($player instanceof Player) {
                    self::giveDebuffBall($player, 1);
                    $sender->sendMessage("Se ha dado un DebuffBall a " . $player->getName());
                } else {
                    $sender->sendMessage("El jugador no está en línea.");
                }
            } else {
                $sender->sendMessage("Uso: /giveball <jugador>");
            }
            return true;
        }
        return false;
    }

    public static function isDebuffBall(Item $item): bool {
        if ($item->getNamedTag()->getTag("isDebuffBall")) {
            return true;
        }
        return false;
    }

    public static function giveDebuffBall(Player $player, int $count): void {
        $item = VanillaItems::SLIMEBALL()->setCustomName(TextFormat::colorize("&r&aDebuffBall"));
        $item->setNamedTag($item->getNamedTag()->setString("isDebuffBall", "true"));
        $item->setCount($count);
        $player->getInventory()->addItem($item);
    }

}
