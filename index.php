<?php
    
    class Embed {
        
        private $pgn;
        
        private $tags;
        
        private $title;
        private $content;
        
        function __construct() {
            
            $this->splitPGN();
            $this->buildTags();
            
            $this->setTitle();
            
        }
        
        private function isGame() {
            
            return ( count( $this->pgn ) > 0 );
            
        }
        
        private function splitPGN() {
            
            $this->pgn = array_filter( preg_split( '/\r\n|\r|\n/', base64_decode( $this->getParam( 'pgn' ) ) ) );
            
        }
        
        private function buildTags() {
            
            foreach( $this->pgn as $line ) {
                
                if( preg_match( '/^\[(.+) "(.{0,})"\]$/', $line, $tag ) ) {
                    
                    $key = strtolower( $tag[1] );
                    $val = in_array( $key, [ 'setup', 'whiteelo', 'blackelo', 'whiteratingdiff', 'blackratingdiff' ] )
                                ? (float) $tag[2]
                                : trim( $tag[2] );
                    
                    $this->tags[ $key ] = $val;
                    
                }
                
            }
            
        }
        
        private function isTag(
            string $tag
        ) {
            
            return array_key_exists( $tag, $this->tags );
            
        }
        
        private function getTag(
            string $tag,
            $default = ''
        ) {
            
            return $this->isTag( $tag ) ? $this->tags[ $tag ] : $default;
            
        }
        
        private function getParam(
            string $param,
            $default = ''
        ) {
           
            return isset( $_GET[ $param ] ) ? $_GET[ $param ] : $default;
            
        }
        
        private function getDate(
            string $prefix = ''
        ) {
            
            if( !$this->isTag( 'date' ) )
                return '';
            
            $date = explode( '.', $this->getTag( 'date' ) );
            
            if( count( $date ) != 3 )
                return $prefix . $this->getTag( 'date' );
            
            else if( $date[1] == '??' )
                return $prefix . $date[0];
            
            else if( $date[2] == '??' )
                return $prefix . date( 'F Y', strtotime( implode( '.', [ '01', $date[1], $date[0] ] ) ) );
            
            else
                return $prefix . date( 'j. F Y', strtotime( implode( '.', array_reverse( $date ) ) ) );
            
        }
        
        private function getLink() {
            
            $link = preg_grep( '/^http.*/', $this->tags );
            
            return count( $link ) > 0 ? array_shift( $link ) : '';
            
        }
        
        private function getEvent() {
            
            return $this->getTag( 'event', 'Chess game embedder' ) . $this->getDate( ' @ ' );
            
        }
        
        private function setTitle() {
            
            $this->title = $this->isGame()
                ? implode( ' • ', array_filter( [
                      $this->getTag( 'white', 'unknown' ) . ' vs. ' . $this->getTag( 'black', 'unknown' ),
                      $this->getTag( 'timecontrol' ),
                      ucfirst( $this->getTag( 'variant', 'standard' ) ),
                      $this->getEvent()
                  ] ) )
                : 'Chess game embedder';
            
        }
        
        public function output() {
            
            print $this->content;
            
        }
        
    }
    
    $embed = new Embed();
    $embed->output();
    
?>
