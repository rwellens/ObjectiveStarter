<?php

    namespace ObjectivePHP\Matcher;

    class Matcher
    {

        const T_STAR          = '*';
        const T_QUESTION_MARK = '?';

        protected $separator = '.';

        /**
         * Checks whether an identifier pattern matches another
         *
         * NOTE: parameter order does'nt matter as the routine is bi-directional
         *
         * @param $filter
         * @param $reference
         *
         * @return bool
         */
        public function match($filter, $reference)
        {

            $separator = $this->separator;

            // normalize ids
            if (is_array($filter))
            {
                $filter = implode($separator, $filter);
            }

            if (is_array($reference))
            {
                $reference = implode($separator, $reference);
            }


            // handle inversed patterns
            $inversedFilter    = substr($filter, 0, 1) == '!';
            $inversedReference = substr($reference, 0, 1) == '!';

            if ($inversedFilter)
            {
                $filter = substr($filter, 1);
            }

            if ($inversedReference)
            {
                $reference = substr($reference, 1);
            }



            if ($inversedFilter XOR $inversedReference)
            {
                return !$this->match($filter, $reference);
            }


            // remove separators from both ends
            $filter    = trim($filter, $separator);
            $reference = trim($reference, $separator);


            // check if either filter or reference contains alternatives

            // match if string are equals
            if ($filter === $reference)
            {
                return true;
            }

            // match if filter or reference is a star
            if ($filter === '*' || $reference === '*')
            {
                return true;
            }


            // split identifiers parts
            $filterParts    = explode($separator, $filter);
            $referenceParts = explode($separator, $reference);


            // if there is no wildcard in identifiers, as they're not equal, they don't match
            if (!$this->containsWildcard($filter . $reference))
            {
                return false;
            }

            $iteration = 0;
            while ($iteration++ < 25)
            {
                // both arrays are empty, they matched!
                if (!$filterParts && !$referenceParts)
                {
                    return true;
                }

                $currentFilterPart    = $this->extractAlternatives(current($filterParts));
                $currentReferencePart = $this->extractAlternatives(current($referenceParts));


                // no more filter or reference part left to compare, didn't match

                // TODO looks useless according to the tests - should be checked however

                // no star case
                if ($currentFilterPart != self::T_STAR && $currentReferencePart != self::T_STAR)
                {
                    switch (true)
                    {

                        case $currentFilterPart === $currentReferencePart:
                        case $currentFilterPart === self::T_QUESTION_MARK:
                        case $currentReferencePart === self::T_QUESTION_MARK:
                        case is_array($currentFilterPart) && is_array($currentReferencePart) && array_intersect($currentFilterPart, $currentReferencePart):
                        case is_array($currentFilterPart) && in_array($currentReferencePart, $currentFilterPart):
                        case is_array($currentReferencePart) && in_array($currentFilterPart, $currentReferencePart):

                            array_shift($filterParts);
                            array_shift($referenceParts);
                            if (!$filterParts xor !$referenceParts)
                            {
                                return false;
                            }
                            continue;

                        default:
                            return false;
                    }
                }

                // star case
                if ($currentFilterPart == self::T_STAR)
                {
                    // get the next T_PART part
                    $referencePartsToSkip = 1;
                    while (true)
                    {
                        $currentFilterPart = current($filterParts);
                        if ($currentFilterPart == self::T_QUESTION_MARK)
                        {
                            $referencePartsToSkip++;
                        }

                        if (!$currentFilterPart || ($currentFilterPart != self::T_STAR && $currentFilterPart != self::T_QUESTION_MARK))
                        {
                            break;
                        }
                        array_shift($filterParts);
                    }

                    if (!$currentFilterPart)
                    {
                        // no other part, match is ok
                        return true;
                    }
                    else
                    {
                        // there is another part - find an occurrence in reference

                        // remove current reference part, matched by the filter star
                        $referenceParts = array_slice($referenceParts, $referencePartsToSkip);

                        // search next occurrence of current filter part
                        $match = array_search($currentFilterPart, $referenceParts);

                        // if not found, look for question mark
                        if ($match === false)
                        {
                            $match = array_search(self::T_QUESTION_MARK, $referenceParts);
                            if ($match === false)
                            {
                                $match = array_search(self::T_STAR, $referenceParts);
                                if ($match === false)
                                {
                                    // echo "RESULT:  no '".trim($currentFilterPart)."' was found in reference\n";
                                    return false;
                                }
                            }
                        }


                        // otherwise, remove all reference parts till equivalence to current filter part
                        $referenceParts = array_slice($referenceParts, $match);
                        continue;
                    }
                }

                if ($currentReferencePart == self::T_STAR)
                {
                    // get the next T_PART part
                    $filterPartsToSkip = 1;
                    array_shift($referenceParts);
                    $currentReferencePart = current($referenceParts);
                    while (true)
                    {
                        $currentReferencePart = current($referenceParts);
                        if ($currentReferencePart == self::T_QUESTION_MARK)
                        {
                            $filterPartsToSkip++;
                        }

                        if (!$currentReferencePart || ($currentReferencePart != self::T_STAR && $currentReferencePart != self::T_QUESTION_MARK))
                        {
                            break;
                        }
                        array_shift($referenceParts);
                    }

                    if (!$currentReferencePart)
                    {
                        // no other part, match is ok
                        return true;
                    }
                    else
                    {
                        // there is another part - find an occurrence in reference

                        // remove current reference part, matched by the filter star
                        $filterParts = array_slice($filterParts, $filterPartsToSkip);

                        // if reference part left, it didn't match
                        if (!$filterParts)
                        {
                            return false;
                        }

                        // search next occurrence of current reference part
                        $match = array_search($currentReferencePart, $filterParts);

                        // if not found, look for question mark or star
                        if ($match === false && array_search(self::T_QUESTION_MARK, $filterParts) === false && array_search(self::T_STAR, $filterParts) === false)
                        {
                            return false;
                        }

                        // otherwise, remove all reference parts till equivalence to current filter part
                        $filterParts = array_slice($filterParts, $match);
                        continue;
                    }
                }
            }

            return false;
        }

        /**
         * Checks whether a string contains a * or a ?
         *
         * @param $string
         *
         * @return bool
         */
        public function containsWildcard($string)
        {
            if (strstr($string, '*'))
            {
                return true;
            }
            elseif (strstr($string, '?')) return true;
            elseif (strstr($string, '[')) return true;

            return false;
        }

        /**
         * Defines nest separator
         *
         * @param string $separator
         */
        public function setSeparator(string $separator) : self
        {
            $this->separator = $separator;

            return $this;
        }

        /**
         * Returns nest separator
         *
         * @param string $separator
         */
        public function getSeparator()
        {
            return $this->separator;
        }

        public function extractAlternatives($identifier)
        {
            if (!(substr($identifier, 0, 1) == '['))
            {
                return $identifier;
            }

            // check alternative validity
            if (substr($identifier, -1, 1) != ']')
            {
                throw new Exception('invalid alternative syntax in filter: [ALT1|ALT2|ALTn]');
            }

            $alternatives = explode("|", substr($identifier, 1, -1));

            // remove empty alternatives
            $alternatives = array_values(array_filter($alternatives));

            // if array is empty, identifier is invalid ( at least one non-empty alternative is mandatory!)
            if (!$alternatives)
            {
                throw new Exception('invalid alternative syntax in filter: [ALT1|ALT2|ALTn]');
            }

            if (array_search('?', $alternatives))
            {
                // if one of the alternatives is a question mark, then we don't need other alternatives
                return '?';
            }

            return $alternatives;
        }
    }