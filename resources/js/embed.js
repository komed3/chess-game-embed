jQuery( document ).ready( function( $ ) {
    
    jQuery.fn.scrollTo = function( element, speed ) {
        
        $( this ).animate( {
            scrollTop: $( this ).scrollTop() - $( this ).offset().top + $( element ).offset().top
        }, speed == undefined ? 'fast' : speed );
        
        return this;
        
    };
    
    function flipBoard() {
        
        $( '.player' ).toggleClass( 'top' ).toggleClass( 'bottom' );
        
        board.flip();
        board.resize();
        
        loadMove( options.start );
        
    }
    
    function loadMove( id = 0, animation = true, scroll = false ) {
        
        var position = id == -1 ? options.position : options.moves.slice( 0, id + 1 ).join( ' ' );
        
        game.load_pgn( position );
        
        board.position( game.fen(), animation );
        
        var newPos = game.board(),
            inCheck = game.in_check();
        
        game.undo();
        
        var oldPos = game.board();
        
        $( '#board .xmove, #board .check' ).remove();
        
        newPos.forEach( function( rank, r ) {
            
            rank.forEach( function( square, f ) {
                
                if( JSON.stringify( square ) != JSON.stringify( oldPos[ r ][ f ] ) ) {
                    
                    $( '#board .square-' + 'abcdefgh'.split( '' )[ f ] + '87654321'.split( '' )[ r ] ).prepend( '<div class="xmove lastmove"></div>' );
                    
                }
                
                if( inCheck && square != null && square.type == 'k' && square.color != game.turn() ) {
                    
                    $( '#board .square-' + 'abcdefgh'.split( '' )[ f ] + '87654321'.split( '' )[ r ] ).prepend( '<div class="check"></div>' );
                    
                }
                
            } );
            
        } );
        
        options.start = id;
        
        $( 'history move' ).removeClass( 'active' );
        $( 'history move[data-id="' + id + '"]' ).addClass( 'active' );
        
        if( scroll ) {
            
            if( $( 'history move.active' ).length > 0 ) {
                
                $( 'history' ).scrollTo( 'move.active' );
                
            } else {
                
                $( 'history' ).animate( { scrollTop: $( 'history' ).offset().top - 50 }, 'fast' );
                
            }
            
        }
        
        $( '.controls button[data-action="first"]' ).prop( 'disabled', id < 0 );
        $( '.controls button[data-action="prev"]' ).prop( 'disabled', id < 0 );
        $( '.controls button[data-action="next"]' ).prop( 'disabled', id == options.moves.length - 1 );
        $( '.controls button[data-action="last"]' ).prop( 'disabled', id == options.moves.length - 1 );
        
    }
    
    var game = new Chess();
    
    var board = Chessboard( 'board', {
        
        position: options.position,
        pieceTheme: './resources/pieces/' + options.piecetheme + '/{piece}.svg',
        showNotation: options.notation
        
    } );
    
    $( 'history move' ).on( 'click', function() {
        
        loadMove( $( this ).data( 'id' ) );
        
    } );
    
    $( '.controls button' ).on( 'click', function() {
        
        switch( $( this ).data( 'action' ) ) {
            
            default:
                break;
            
            case 'flip':
                flipBoard();
                break;
            
            case 'first':
                loadMove( -1, true, true );
                break;
            
            case 'prev':
                loadMove( options.start - 1, true, true );
                break;
            
            case 'next':
                loadMove( options.start + 1, true, true );
                break;
            
            case 'last':
                loadMove( options.moves.length - 1, true, true );
                break;
            
        }
        
    } );
    
    $( window ).resize( function() {
        
        board.resize();
        loadMove( options.start );
        
    } );
    
    if( options.inline )
        $( 'history' ).addClass( 'inline' );
    
    if( options.orientation == 'black' )
        flipBoard();
    
    setTimeout( function() {
        
        board.resize();
        loadMove( options.start, false, true );
        
    }, 250 );
    
} );
