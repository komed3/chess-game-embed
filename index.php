<?php
    
    class Embed {
        
        private $pgn = [];
        private $tags = [];
        
        private $history = [];
        private $moves = [];
        
        private $result = [];
        
        private $title;
        private $content;
        
        function __construct() {
            
            $this->splitPGN();
            $this->buildTags();
            
            $this->title = $this->getTitle();
            
            if( $this->isGame() ) {
                
                $this->buildMoves();
                $this->setResult();
                
                $this->run();
                
            } else {
                
                $this->content = '<div class="empty"><div>' .
                    '<h1>' . $this->title . '</h1>' .
                    '<p>Embedding chess games on your website by using the free tool from ' .
                            '<a href="https://thekingsgame.de/embed/" target="_blank">thekingsgame.de</a></p>' .
                    '<p>' . $this->donate() . '</p>' .
                '</div></div>';
                
            }
            
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
        
        private function buildMoves() {
            
            $notation = str_replace(
                [ '.', '  ' ],
                [ '. ', ' ' ],
                preg_replace(
                    '/\{(.+)\}/U', '',
                    str_replace(
                        [ '–', '0-0-0', '0-0' ],
                        [ '-', 'O-O-O', 'O-O' ],
                        end( $this->pgn )
                    )
                )
            );
            
            $i = 0;
            $co = 'w';
            
            foreach( array_filter( explode( ' ', $notation ) ) as $move ) {
                
                if( preg_match( '/(.+)[:|-](.+)/', $move ) && strpos( $move, 'O' ) === false ) {
                    
                    continue;
                    
                } else if( preg_match( '/^([0-9]{1,})\./', $move, $idx ) ) {
                    
                    $this->history[] = '<idx>' . $idx[1] . '</idx>';
                    
                    $no = $idx[1];
                    $co = 'w';
                    
                } else {
                    
                    $this->history[] = '<move data-id="' . $i . '" data-no="' . $no . '" data-co="' . $co . '">' . $move . '</move>';
                    
                    $this->moves[ $i ] = ( $co == 'w' ? $no . '. ' : '' ) . $move;
                    
                    $co = 'b';
                    $i++;
                    
                }
                
            }
            
        }
        
        private function setResult() {
            
            $this->result = [
                '*' => [ -1, -1 ],
                '0-1' => [ 0, 1 ],
                '1-0' => [ 1, 0 ],
                '1/2-1/2' => [ 2, 2 ]
            ][ $this->getTag( 'result', '*' ) ];
            
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
        
        private function getTitle(
            bool $link = false
        ) {
            
            return $this->isGame()
                ? implode( ' • ', array_filter( [
                      $this->getTag( 'white', 'unknown' ) . ' vs. ' . $this->getTag( 'black', 'unknown' ),
                      $this->getTag( 'timecontrol' ),
                      ucfirst( $this->getTag( 'variant', 'standard' ) ),
                      $link && strlen( $this->getLink() ) > 0
                          ? '<a href="' . $this->getLink() . '" target="_blank">' . $this->getEvent() . '</a>'
                          : $this->getEvent()
                  ] ) )
                : 'Chess game embedder';
            
        }
        
        private function getPlayer(
            string $color = 'white'
        ) {
            
            return '<div class="player ' . $color . '">' .
                '<div class="score">' .
                    $this->getScore( $color ) .
                '</div>' .
                '<div class="name">' .
                    $this->getPlayerTitle( $color ) .
                    $this->getTag( $color ) .
                '</div>' .
                '<div class="rating">' .
                    $this->getRating( $color ) .
                    $this->getRatingDiff( $color ) .
                '</div>' .
            '</div>';
            
        }
        
        private function getScore(
            string $color = 'white'
        ) {
            
            return [ -1 => '–', 0 => '0', 1 => '1', 2 => '½' ][ $this->result[ [ 'white' => 0, 'black' => 1 ][ $color ] ] ];
            
        }
        
        private function getTermination() {
            
            return [
               -1 => 'ongoing game or unknown termination',
                0 => 'Black wins',
                1 => 'White wins',
                2 => 'draw'
            ][ $this->result[0] ];
            
        }
        
        private function getPlayerTitle(
            string $color = 'white'
        ) {
            
            return $this->isTag( $color . 'title' ) && $this->getTag( $color . 'title' ) != '-'
                ? '<span class="title">' . $this->getTag( $color . 'title' ) . '</span>'
                : '';
            
        }
        
        private function getRating(
            string $color = 'white'
        ) {
            
            $rating = $this->getTag( $color . 'elo', 0 );
            
            return $rating > 0 ? round( $rating ) : '';
            
        }
        
        private function getRatingDiff(
            string $color = 'white'
        ) {
            
            $diff = $this->getTag( $color . 'ratingdiff', 0 );
            
            return $diff != 0 ? '<span class="diff ' . ( $diff < 0 ? 'bad' : 'good' ) . '">' . abs( round( $diff ) ) . '</span>' : '';
            
        }
        
        private function getHistory() {
            
            return '<history><moves>' . implode( '', $this->history ) . '</moves>' . $this->getResult() . '</history>';
            
        }
        
        private function getResult() {
            
            return '<div class="result">' .
                '<div class="score">' . $this->getScore( 'white' ) . ':' . $this->getScore( 'black' ) . '</div>' .
                '<div class="termination">' . $this->getTermination() . '</div>' .
            '</div>';
            
        }
        
        private function donate() {
            
            return '<a href="https://paypal.me/komed3" class="donate" target="_blank" title="Donate and help"><i data-icon=""></i></a>';
            
        }
        
        private function run() {
            
            $this->content = '<div class="container">' .
                $this->getPlayer( 'white' ) .
                $this->getPlayer( 'black' ) .
                '<div class="board"><div id="board"></div></div>' .
                '<div class="aside">' .
                    $this->getHistory() .
                    '<div class="controls">' .
                        '<button data-action="first" title="First"><i data-icon="W"></i></button>' .
                        '<button data-action="prev" title="Prev"><i data-icon="Y"></i></button>' .
                        '<button data-action="flip" title="Flip board"><i data-icon="B"></i></button>' .
                        '<button data-action="next" title="Next"><i data-icon="X"></i></button>' .
                        '<button data-action="last" title="Last"><i data-icon="V"></i></button>' .
                    '</div>' .
                '</div>' .
            '</div>' .
            '<footer>' .
                '<div class="credits">' .
                    implode( ' • ', [
                        $this->getTitle( true ),
                        '&copy; ' . date( 'Y' ) . ' <a href="https://thekingsgame.de" target="_blank">thekingsgame.de</a>',
                        '<a href="https://github.com/jhlywa/chess.js" target="_blank">chess.js</a>',
                        '<a href="https://chessboardjs.com/" target="_blank">chessboard.js</a>'
                    ] ) .
                '</div>' .
                '<div class="donate">' . $this->donate() . '</div>' .
            '</footer>';
            
        }
        
        public function output() {
            
            print '<!DOCTYPE html>' .
            '<html lang="en">' .
                '<head>' .
                    '<meta charset="utf-8" />' .
                    '<meta name="viewport" content="width=device-width, user-scalable=no" />' .
                    '<meta name="author" content="thekingsgame.de" />' .
                    '<title>' . $this->title . '</title>' .
                    '<script src="./resources/js/jquery.min.js"></script>' .
                    '<script src="./resources/js/chess.min.js"></script>' .
                    '<script src="./resources/js/chessboard.min.js"></script>' .
                    '<script src="./resources/js/embed.js"></script>' .
                '</head>' .
                '<body>' .
                    $this->content .
                '</body>' .
            '</html>';
            
        }
        
    }
    
    $embed = new Embed();
    $embed->output();
    
?>
