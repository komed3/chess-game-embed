# chess-game-embed

Embed chess games from PGN with multiple setting options. Uses [chess.js](https://github.com/jhlywa/chess.js) for game logic, queries and move validation and [chessboard.js](https://chessboardjs.com/) for displaying the chessboard.

Use the embedder free of charge and unlimited at [https://embed.thekingsgame.de](https://embed.thekingsgame.de)

# Usage

#### Arguments

+ ``pgn``: PGN of the chess game to be embedded, encoded to Base64 format (**required**)
+ ``start``: Start position as index of the array of moves starting from 0 (not to be confused with half moves)
+ ``orientation``: Standard orientation of the chessboard; ``white`` (default) or ``black`` allowed as values
+ ``notation``: If ``notation=0``, suppress the chessboard notation (1..8 and a..h)
+ ``inline``: If ``inline=1``, use the inline notation
+ ``theme``: Set the page theme; allowed values are ``light`` (default) and ``dark``
+ ``pieceset``: Set the piece theme; allowed values are ``alpha``, ``california``, ``cardinal``, ``cburnett`` (default), ``chess7``, ``chessnut``, ``companion``, ``dubrovny``, ``fantasy``, ``fresca``, ``gioco``, ``governor``, ``icpieces``, ``kosal``, ``leipzig``, ``letter``, ``libra``, ``maestro``, ``merida``, ``pirouetti``, ``pixel``, ``reillycraig``, ``riohacha``, ``shapes``, ``spatial``, ``staunty``, ``tatiana``

#### PGN format guidelines

+ Write the entire move notation in one line at the end of the PGN.
+ Use the short notation of the moves, e.g. ``Re4``, ``Qh1+``, ``exd5`` ...
+ Simple brackets ``{...}`` are automatically removed.
+ If possible, do not use complex comments within the move notation.

# Example

See the following example from chess game: [**Kasparov -- Topalov, Wijk aan Zee 1999**](https://embed.thekingsgame.de/?pgn=W0V2ZW50ICJJdCAoY2F0LjE3KSJdCltTaXRlICJXaWprIGFhbiBaZWUgKE5ldGhlcmxhbmRzKSJdCltEYXRlICIxOTk5Lj8/Lj8/Il0KW1JvdW5kICI0Il0KW1doaXRlICJLYXNwYXJvdiwgR2FycnkgKFJVUykiXQpbQmxhY2sgIlRvcGFsb3YsIFZlc2VsaW4gKEJVTCkiXQpbUmVzdWx0ICIxLTAiXQpbRUNPICJCMDciXQpbV2hpdGVFbG8gIjI4NTEiXQpbQmxhY2tFbG8gIjI2OTAiXQpbQW5ub3RhdG9yICIiXQpbU291cmNlICIiXQpbUmVtYXJrICJJIl0KCjEuIGU0IGQ2IDIuIGQ0IE5mNiAzLiBOYzMgZzYgNC4gQmUzIEJnNyA1LiBRZDIgYzYgNi4gZjMgYjUgNy4gTmdlMiBOYmQ3IDguIEJoNiBCeGg2IDkuIFF4aDYgQmI3IDEwLiBhMyBlNSAxMS4gTy1PLU8gUWU3IDEyLiBLYjEgYTYgMTMuIE5jMSBPLU8tTyAxNC4gTmIzIGV4ZDQgMTUuIFJ4ZDQgYzUgMTYuIFJkMSBOYjYgMTcuIGczIEtiOCAxOC4gTmE1IEJhOCAxOS4gQmgzIGQ1IDIwLiBRZjQrIEthNyAyMS4gUmhlMSBkNCAyMi4gTmQ1IE5ieGQ1IDIzLiBleGQ1IFFkNiAyNC4gUnhkNCBjeGQ0IDI1LiBSZTcrIEtiNiAyNi4gUXhkNCsgS3hhNSAyNy4gYjQrIEthNCAyOC4gUWMzIFF4ZDUgMjkuIFJhNyBCYjcgMzAuIFJ4YjcgUWM0IDMxLiBReGY2IEt4YTMgMzIuIFF4YTYrIEt4YjQgMzMuIGMzKyBLeGMzIDM0LiBRYTErIEtkMiAzNS4gUWIyKyBLZDEgMzYuIEJmMSBSZDIgMzcuIFJkNyBSeGQ3IDM4LiBCeGM0IGJ4YzQgMzkuIFF4aDggUmQzIDQwLiBRYTggYzMgNDEuIFFhNCsgS2UxIDQyLiBmNCBmNSA0My4gS2MxIFJkMiA0NC4gUWE3&inline=1)

#### Used PGN

    [Event "It (cat.17)"]
    [Site "Wijk aan Zee (Netherlands)"]
    [Date "1999.??.??"]
    [Round "4"]
    [White "Kasparov, Garry (RUS)"]
    [Black "Topalov, Veselin (BUL)"]
    [Result "1-0"]
    [ECO "B07"]
    [WhiteElo "2851"]
    [BlackElo "2690"]
    [Annotator ""]
    [Source ""]
    [Remark "I"]
    
    1. e4 d6 2. d4 Nf6 3. Nc3 g6 4. Be3 Bg7 5. Qd2 c6 6. f3 b5 7. Nge2 Nbd7 8. Bh6 Bxh6 9. Qxh6 Bb7 10. a3 e5 11. O-O-O Qe7 12. Kb1 a6 13. Nc1 O-O-O 14. Nb3 exd4 15. Rxd4 c5 16. Rd1 Nb6 17. g3 Kb8 18. Na5 Ba8 19. Bh3 d5 20. Qf4+ Ka7 21. Rhe1 d4 22. Nd5 Nbxd5 23. exd5 Qd6 24. Rxd4 cxd4 25. Re7+ Kb6 26. Qxd4+ Kxa5 27. b4+ Ka4 28. Qc3 Qxd5 29. Ra7 Bb7 30. Rxb7 Qc4 31. Qxf6 Kxa3 32. Qxa6+ Kxb4 33. c3+ Kxc3 34. Qa1+ Kd2 35. Qb2+ Kd1 36. Bf1 Rd2 37. Rd7 Rxd7 38. Bxc4 bxc4 39. Qxh8 Rd3 40. Qa8 c3 41. Qa4+ Ke1 42. f4 f5 43. Kc1 Rd2 44. Qa7
