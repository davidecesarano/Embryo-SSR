<?php

    /**
     * SSR
     *
     * Server side rendering JavaScript PHP application
     * with V8Js.
     *
     * @author Davide Cesarano <davide.cesarano@unipegaso.it>
     * @link   https://github.com/davidecesarano/embryo-serversiderendering
     */

    namespace Embryo\ServerSideRendering;

    class SSR
    {
        /**
         * @var V8Js $v8js
         */
        private $v8js;
        
        /**
         * @var bool $enabled
         */
        private $enabled = true;
        
        /**
         * @var array $env
         */
        private $env = [];
        
        /**
         * @var string|array $entry
         */
        private $entry;

        /**
         * @var array $context
         */
        private $context = [];
        
        /**
         * @var string $script
         */
        private $script = '';

        /**
         * @var string $fallback
         */
        private $fallback = '';

        /**
         * Set V8Js engine.
         * 
         * @param V8Js $v8js
         */
        public function __construct($v8js)
        {
            $this->v8js = $v8js;
        }

        /**
         * Enabled server side rendering.
         * Default is true.
         * 
         * @param bool $enbled
         */
        public function enabled(bool $enabled = true): self
        {
            $this->enabled = $enabled;
            return $this;
        }

        /**
         * Set env vars.
         * 
         * @param array $env 
         * @return self
         */
        public function env(array $env): self
        {
            $this->env = $env;
            return $this;
        }

        /**
         * Set path server script\s. 
         * 
         * @param  string|array $entry 
         * @return self 
         * @throws InvalidArgumentException
         */
        public function entry($entry): self
        {
            if (!is_string($entry) && !is_array($entry)) {
                throw new \InvalidArgumentException("Entry file\s must be an array or a string");
            }
            $this->entry = $entry;
            return $this;
        }

        /**
         * Set context.
         * 
         * @param array $context 
         * @return self
         */
        public function context(array $context): self
        {
            $this->context = $context;
            return $this;
        }

        /**
         * Set Javascript script. 
         * 
         * @param string $script 
         * @return self
         */
        public function script(string $script): self 
        {
            $this->script = $script;
            return $this;
        }

        /**
         * Set fallback html when server side rendering 
         * is disabled.
         * 
         * @param string $fallback 
         * @return self
         */
        public function fallback(string $fallback): self
        {
            $this->fallback = $fallback;
            return $this;
        }

        /**
         * Execute and render scripts.
         * 
         * @return string
         */
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
                $this->script,
                implode(';', $entry)
            ]);
            
            ob_start();
            $this->v8js->executeString($javascript);
            return ob_get_clean();
        }
    }
