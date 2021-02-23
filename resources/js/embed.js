jQuery( document ).ready( function( $ ) {
    
    function orientation( color = 'white' ) {
        
        $( '.player.white' ).addClass( color == 'white' ? 'bottom' : 'top' );
        $( '.player.black' ).addClass( color == 'black' ? 'bottom' : 'top' );
        
        board.orientation( color );
        
    }
    
    var game = new Chess();
    
    var board = Chessboard( 'board', {
        
        position: options.position,
        pieceTheme: './resources/pieces/' + options.piecetheme + '/{piece}.svg',
        showNotation: options.notation
        
    } );
    
    if( options.inline )
        $( 'history' ).addClass( 'inline' );
    
    orientation( options.orientation );
    
} );
