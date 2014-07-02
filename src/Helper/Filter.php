<?php
Namespace Helper
{
    define('FILTER_STRUCT_FORCE_ARRAY', 1);
    define('FILTER_STRUCT_TRIM',        2);
    define('FILTER_STRUCT_FULL_TRIM',   4);

    Class Filter
    {
        public function __construct()
        {
            /** ... */
        }

        /**
         * Filter difficult super array structures
         *
         * @param integer $type    Constant like INPUT_XXX
         * @param array   $default Default structure of the specified super global var
         *
         *  Following bitmasks are available:
         *  + FILTER_STRUCT_FORCE_ARRAY - Force 1 dimensional array.
         *  + FILTER_STRUCT_TRIM        - Trim by ASCII control chars.
         *  + FILTER_STRUCT_FULL_TRIM   - Trim by ASCII control chars,
         *                                full-width and no-break space.
         *
         * @return array            The value of the filtered super global var
         * @throws \LogicException
         */
        public function filterStructUtf8($type, array $default)
        {
            static $func            = __METHOD__,
                   $trim            = '[\\x0-\x20\x7f]',
                   $ftrim           = '[\\x0-\x20\x7f\xc2\xa0\xe3\x80\x80]',
                   $recStatic       = false; // 0

            if (! $recursive = $recStatic)
            {
                $types = [
                    INPUT_GET       => $_GET,
                    INPUT_POST      => $_POST,
                    INPUT_COOKIE    => $_COOKIE,
                    INPUT_REQUEST   => $_REQUEST,
                ];

                if (! isset($types[(int) $type]))
                    Throw New \LogicException('Unknown super-global variable type');

                $var = $types[(int) $type];
                $recStatic = true;
            } else {
                $var = $type;
            }

            $data = [];
            foreach ($default AS $key => $value)
            {
                if ($isInt = is_int($value))
                {
                    if (! ($value | (FILTER_STRUCT_FORCE_ARRAY | FILTER_STRUCT_FULL_TRIM | FILTER_STRUCT_TRIM)))
                    {
                        $recStatic = false;
                        Throw New \LogicException('Unknown bitmask');
                    }

                    if ($value & FILTER_STRUCT_FORCE_ARRAY)
                    {
                        $tmp = [];

                        if (isset($var[$key]))
                        {
                            foreach ((array) $var[$key] AS $k => $v)
                            {
                                if (! preg_match('//u', $k))
                                    continue;

                                $value &= FILTER_STRUCT_FULL_TRIM | FILTER_STRUCT_TRIM;

                                $tmp   += [
                                    $k => $value ? $value : ''
                                ];
                            }
                        }
                        $value = $tmp;
                    }
                }

                if ($isset = isset($var[$key]) and is_array($value))
                {
                    $data[$key] = $func($var[$key], $value);
                }

                elseif (! $isset || is_array($var[$key]))
                {
                    $data[$key] = null;
                }

                elseif ($isInt && $value & FILTER_STRUCT_FULL_TRIM)
                {
                    $data[$key] = preg_replace("/\A{$ftrim}++|{$ftrim}++\z/u", '', $var[$key]);
                }

                elseif ($isInt && $value & FILTER_STRUCT_TRIM)
                {
                    $data[$key] = preg_replace("/\A{$trim}++|{$trim}++\z/u", '', $var[$key]);
                }

                else
                {
                    $data[$key] = preg_replace('//u', '', $var[$key]);
                }
                
                if (null === $data[$key])
                    $data[$key] = $isInt ? '' : $value;
            }

            if (! $recursive)
                $recStatic = false;

            return $data;
        }
    }
}