<?php

namespace App\ValueObjects;

class TempTableColumn {
    
    public function __construct(
        public string $name,
        public string $type,
        public ?string $options = null
    ) {
    }
}
