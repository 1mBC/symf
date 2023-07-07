<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class Matching
{
    private $entityManagerInterface;

    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->entityManagerInterface = $entityManagerInterface;
    }

    public function getMatches($user_account_id): array
    {
        ini_set('memory_limit', '4096M');  // Augmente la limite de mémoire à 1024 Mo

        $allUsersChoicesBin = $this->getAllUsersChoicesBin();

        $userBin = $allUsersChoicesBin[$user_account_id];
        unset($allUsersChoicesBin[$user_account_id]);

        $matches = array();
        foreach($allUsersChoicesBin as $mid => $matchBin)
        {
            $matches[$mid]['score'] = $this->countCommonBitsIt($userBin, $matchBin);
            $matches[$mid]['bin'] = $matchBin;
        }
        uasort($matches, function ($a, $b) {
            return $b['score'] - $a['score'];
        });
        
        return $matches;
    }

    public function getAllUsersChoicesBin(): array
    {
        $db = $this->entityManagerInterface->getConnection();

        $query = "SELECT account_choice.*, choice.position 
        FROM account_choice 
        INNER JOIN choice on account_choice.choice_id = choice.id 
        ORDER BY account_id";
        $allUsersChoices = $db->executeQuery($query)->fetchAllAssociative();

        $query = "SELECT * FROM choice ORDER BY position";
        $allChoices = $db->executeQuery($query)->fetchAllAssociative();

        $query = "SELECT * FROM account ORDER BY id";
        $allAccounts = $db->executeQuery($query)->fetchAllAssociative();

        $emptyChoices = array_fill(0, count($allChoices), 0);
        $allUsersChoicesArrayBin = array();
        foreach($allAccounts as $account){
            $allUsersChoicesArrayBin[$account['id']] = $emptyChoices;
        }

        foreach($allUsersChoices as $userChoice)
        {
            $allUsersChoicesArrayBin[$userChoice['account_id']][$userChoice['position']-1] = 1;
        }

        $query = "SELECT * FROM choice ORDER BY position";
        $allChoices = $db->executeQuery($query)->fetchAllAssociative();

        $allUsersChoicesBin = array_map(function($choices) {
            return implode("", $choices);
        }, $allUsersChoicesArrayBin);
        

        return $allUsersChoicesBin;
    }
    

    //Returns systematically 0 .. even with only 100 choices .. maybe try to split it ?
    public function countCommonBitsDec(string $binary1, string $binary2) {
        // Convert binary strings to decimal integers
        $num1 = bindec($binary1);
        $num2 = bindec($binary2);
        
        // Perform bitwise AND operation
        $result = $num1 & $num2;
        
        // Convert result back to binary string
        $binaryResult = decbin($result);
        
        // Count number of 1s (common bits) in the result
        $commonBits = substr_count($binaryResult, '1');
        
        return $commonBits;
    }

    //works well and seems fast (for now)
    function countCommonBitsIt(string $binary1, string $binary2) {
        $len = strlen($binary1);
        $commonBits = 0;
        for($i = 0; $i < $len; $i++){
            if($binary1[$i] === '1' && $binary2[$i] === '1'){
                $commonBits++;
            }
        }
        return $commonBits;
    }
    

    //also returns 0 systematically ... it was GPT suggestion 
    public function countCommonBitsBCMath(string $binary1, string $binary2) {
        // Convert binary strings to decimal numbers
        $num1 = $this->bchexdec(bin2hex(pack('H*', $binary1)));
        $num2 = $this->bchexdec(bin2hex(pack('H*', $binary2)));
    
        // Perform bitwise AND operation
        $result = $this->bcand($num1, $num2);
    
        // Convert result back to binary string
        $binaryResult = base_convert($result, 10, 2);
    
        // Count number of 1s (common bits) in the result
        $commonBits = substr_count($binaryResult, '1');
    
        return $commonBits;
    }
    private function bchexdec($hex) {
        $dec = '0';
        $len = strlen($hex);
        for ($i = 1; $i <= $len; $i++)
            $dec = bcadd($dec, bcmul(strval(hexdec($hex[$i-1])), bcpow('16', strval($len-$i))));
        return $dec;
    }
    private function bcand($a,$b) {
        $a = $this->bchexdec(bin2hex(pack('H*', $a)));
        $b = $this->bchexdec(bin2hex(pack('H*', $b)));
        return $a & $b;
    }
    
        
}