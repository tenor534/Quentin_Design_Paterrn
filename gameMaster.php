<?php

    /**
     * Interfaces : Commençons par créer une interface qui définit le comportement de base commun à tous ces objets. 
     * Par exemple, nous pouvons créer une Interactableinterface qui inclut une méthode comme interact()
     * Développeur : Solofo RAKOTONDRABE
     */

    interface Interactable {
        
        public function generateRandomValue();
    
        public function chooseValue();
    
        public function getCurrentValue();
    }

    //Dice classe : implémente l' Interactableinterface et définit le comportement pour lancer un dé.
    class Dice implements Interactable {

        private int $faces;         //Nombre de face(s) du dé
        private int $currentValue;  //Valeur actuelle enregistrée

        public function __construct(int $faces, int $currentValue)
        {
            $this->faces        = $faces;
            $this->currentValue = $currentValue;
        }

        public function generateRandomValue() : int
        {
            //echo "Dice rolled: " . mt_rand(1, $this->faces);
            return mt_rand(1, $this->faces);
            
        }

        public function chooseValue()
        {
            $this->currentValue = $this->generateRandomValue();
        }

        public function getCurrentValue()
        {
            return $this->currentValue;
        }
    }

    //Coin classe : implémente également l' Interactableinterface mais fournit un comportement pour lancer une pièce de monnaie.
    class Coin implements Interactable {

        private int $totalThrows;
        private int $headsCount;
        private int $currentValue = 0;  //Valeur actuelle enregistrée

        public function __construct(int $totalThrows, int $headsCount)
        {
            $this->totalThrows      = $totalThrows;
            $this->headsCount       = $headsCount;            
        }

        public function generateRandomValue()
        {
            $this->totalThrows++;
            $result = mt_rand(1, 2); // Génère aléatoirement 1 ou 2 (pile ou face)
            if ($result === 1) {
                $this->headsCount++;
            }
            return $result;
        }

        public function chooseValue()
        {
            // Pour un dé, la valeur actuelle dépend du nombre de fois que "1" a été obtenu
            if ($this->headsCount === $this->totalThrows) {
                $this->currentValue =  1;
            } else {
                $this->currentValue =  2;
            }
        }

        public function getCurrentValue()
        {
            return $this->currentValue;
        }
    }

    //Deck classe : Pourrait représenter un jeu de cartes. Il implémente également l' Interactableinterface.

    class Deck implements Interactable {

        private int $numberOfColors;
        private int $numberOfValues;
        private int $currentColor = 0;
        private int $currentValue = 0;
    
        public function __construct(int $numberOfColors, int $numberOfValues)
        {
            $this->numberOfColors = $numberOfColors;
            $this->numberOfValues = $numberOfValues;
        }
    
        public function generateRandomValue()
        {
            $this->currentColor = mt_rand(1, $this->numberOfColors);
            $this->currentValue = mt_rand(1, $this->numberOfValues);
            return $this->currentValue + ($this->currentColor * $this->numberOfValues);
        }
    
        public function chooseValue()
        {
            // Logique de choix de valeur de carte
            return $this->generateRandomValue();
        }
    
        public function getCurrentValue() : string
        {
            // Renvoie la couleur et la valeur actuelle
            return "Couleur : {$this->currentColor}, Valeur : {$this->currentValue}";
        }
    }

    //GameMaster class : Cette classe peut coordonner les interactions entre ces objets.

    class GameMaster {

        private array $diceInstances = []; //Liste d'instance de dé
        private array $deckInstances = []; //Liste d'instance de carte
        private array $coinInstances = []; //Liste d'instance de pièce de monnaie 

        public function __construct(array $diceInstances, array $deckInstances, array $coinInstances, private ?Interactable $randGenInteractable = NULL)
        {
            $this->diceInstances = $diceInstances;
            $this->deckInstances = $deckInstances;
            $this->coinInstances = $coinInstances;
            //
            $this->randGenInteractable = $this->getRandomGenerator();
        }
    
        //un GameMaster peut effectuer des tirages via la méthode `pleaseGiveMeACrit`, avec la valeur du coup critique 
        //en paramètre exprimée en pourcentage
        public function pleaseGiveMeACrit($percentage) {
            
            $result = $this->randGenInteractable->generateRandomValue();
    
            $maxValue = $this->randGenInteractable->generateRandomValue(); // Utilisez cette valeur pour calculer le pourcentage
            echo "($result / $maxValue) * 100 < $percentage </br>";
            return ($result / $maxValue * 100) < $percentage;
        }
    
        //Le GameMaster sélectionne l'une des instances de Dice / Deck et Coin au hasard et renvoie vrai si le résultat obtenu 
        //divisé par le nombre maximum de valeurs est inférieur au pourcentage en paramètre.
        private function getRandomGenerator() : Interactable
        {
            $randomIndex = mt_rand(0, count($this->diceInstances) + count($this->deckInstances) + count($this->coinInstances) - 1);
    
            if ($randomIndex < count($this->diceInstances)) {
                echo "Dice randomized</br>";
                return $this->diceInstances[$randomIndex];
            } elseif ($randomIndex < count($this->diceInstances) + count($this->deckInstances)) {
                echo "Deck randomized</br>";
                $deckIndex = $randomIndex - count($this->diceInstances);
                return $this->deckInstances[$deckIndex];
            } else {
                echo "Coin randomized</br>";
                $coinIndex = $randomIndex - count($this->diceInstances) - count($this->deckInstances);
                return $this->coinInstances[$coinIndex];
            }
        }

        /*public function playGame(Interactable $object) {
            return $object->generateRandomValue();
        }*/
    }

    /**
     * Utilisation: Cette configuration permet un comportement polymorphe, où des objets de différentes classes 
     * (Dice, Coin, Deck) peuvent être traités de manière interchangeable via l' Interactableinterface.
     * Cette structure permet une manière cohérente d'interagir avec différents objets tout en utilisant la même 
     * méthode ( interact() dans ce cas) et en profitant du polymorphisme de PHP. 
     */
    
    // Exemple d'utilisation de la classe GameMaster
    $dice1 = new Dice(6,0);
    $dice2 = new Dice(12,1);
    $deck1 = new Deck(3, 18);
    $deck2 = new Deck(4, 13);
    $coin1 = new Coin(0, 0);
    $coin2 = new Coin(0, 0);

    $gameMaster = new GameMaster([$dice1, $dice2], [$deck1, $deck2], [$coin1, $coin2], NULL);

    $percentage = 75; // Pourcentage du coup critique
    //$percentage = 55; // Pourcentage du coup critique
    $result = $gameMaster->pleaseGiveMeACrit($percentage);

    if ($result) {
        echo "</br>Coup critique réussi !\n";
    } else {
        echo "</br>Coup critique raté.\n";
    }


?>