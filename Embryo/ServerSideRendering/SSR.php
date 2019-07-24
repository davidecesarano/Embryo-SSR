<?php

    /**
     * RequestHandler
     *
     * Create a collection of middleware,
     * handle the request and return a response.
     *
     * @author Davide Cesarano <davide.cesarano@unipegaso.it>
     * @link   https://github.com/davidecesarano/embryo-middleware
     * @see    https://github.com/php-fig/http-server-handler/blob/master/src/RequestHandlerInterface.php
     */

    namespace Embryo\ServerSideRendering;

    class SSR
    {
        private $v8js;
        private $enabled = true;
        private $env = [];
        private $entry;
        private $context = [];
        private $script = '';

        public function __construct($v8js)
        {
            $this->v8js = $v8js;
        }

        public function enabled(bool $enabled = true)
        {
            $this->enabled = $enabled;
            return $this;
        }

        public function env(array $env): self
        {
            $this->env = $env;
            return $this;
        }

        public function entry($entry): self
        {
            if (!is_string($entry) && !is_array($entry)) {
                throw new \InvalidArgumentException("Entry file\s must be an array or a string");
            }
            $this->entry = $entry;
            return $this;
        }

        public function context(array $context): self
        {
            $this->context = $context;
            return $this;
        }

        public function script(string $script): self 
        {
            $this->script = $script;
            return $this;
        }

        public function render()
        {
            if (!$this->enabled) {
                return $this->fallback;
            }

            // process.env vars
            $process = [];
            foreach ($this->env as $key => $value) {
                $process[] = 'process.env.'.$key.' = "'.$value.'"';
            }

            // context
            $context = (empty($this->context)) ? '{}' : json_encode($this->context);
            
            // emtry server file\s
            $entry = [];
            if (!empty($this->entry)) {
                if (is_array($this->entry)) {
                    foreach ($this->entry as $file) {
                        $entry[] = file_get_contents($file);
                    }
                } else {
                    $entry[] = file_get_contents($this->entry);
                }
            }

            $javascript = implode(';', [
                'var process = process || { env: {} }',
                implode(';', $process),
                "var context = {$context}",
                implode(';', $entry),
                $this->script
            ]);

            return $this->v8js->executeString($javascript);
        }
    }
