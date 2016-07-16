        
  
      /* 
        Lo script snake.js è utilizzato per realizzare il gioco omonimo.
        Utilizza un canvas html per la grafica, e window.requestAnimationFrame
        per le animazioni
      */

        //Costanti
        //Righe e colonne in cui sarà diviso il canvas
        var COLS = 26, ROWS = 26;
        //ids per i diversi elementi sulla griglia del canvas
        var EMPTY = 0, SNAKE = 1, FRUIT = 2;
        //Direzioni
        var LEFT = 0, UP = 1, RIGHT = 2, DOWN = 3;
        //Keycodes per l'input
        var UP_CODE = 38, LEFT_CODE = 37, RIGHT_CODE = 39, DOWN_CODE = 40;

        /* 
          L'oggetto grid è la griglia in cui sarà diviso il canvas
        */
        var grid = {
            //la larghezza ovvero il numero di righe
            width : null,
            //la lunghezza ovvero il numero di colonne
            height : null,
            //L'array che permette di gestire la griglia
            _grid : null,

            /*
                init prende come input c, il numero delle colonne,
                r, il numero delle righe, e inizializza _grid come
                un array di array (griglia), pieno di elementi d.
             */
            init: function(d,c,r) {
                this.width = r;
                this.height = c;
                this._grid = new Array();
                for(var x = 0; x < c; x++) {
                    this._grid.push(new Array());
                    for(var y = 0; y < r; y++) {
                        this._grid[x].push(d);
                    }
                }
            },

            /*
                Setta una precisa posizione della griglia x y con
                il valore val
             */
            set: function(val, x, y) {
                this._grid[x][y] = val;
            },

            /*
                Ritorna il valore della posizione x,y della griglia
             */
            get: function(x,y) {
                return this._grid[x][y];
            }

        }

        /* 
          L'oggetto snake rappresenta il serpente. Esso è realizzato
          come una coda FIFO. Quando il serpente si allunga, il suo nuovo
          elemento sarà inserito in testa
        */
        var snake = {
            //La direzione del serpente
            direction : null,
            //L'ultimo elemento inserito
            last : null,
            //l'array coda
            _queue : null,

            /*
                Inizializza lo snake, dandogli direzione
                primo elemento e inizializzando la _queue
             */
            init: function(d, x, y) {
                this.direction = d;
                this._queue = new Array();
                this.insert(x,y);
            },

            /*
                Insert inserisce un nuovo elemento allo snake,
                mettendolo per primo nell'array. last è la testa
                del serpente
            */
            insert: function(x,y) {
                this._queue.unshift({x:x, y:y});
                this.last = this._queue[0];
            },

            /*
                La funzione remove rimuove l'ultimo
                elemento del serpente
            */
            remove: function() {
                return this._queue.pop();
            }
        }

        /*
            Funzione che tramite Math.random sceglie una posizione casuale
            tra le caselle della grid che sono EMPTY.
         */
        function setFood() {
            //Un array che conterrà elementi "posizione", con una x e una y,
            //presi dalla griglia con valore EMPTY
            var empty = new Array();
            for(var x = 0; x < grid.width; x++) {
                for(var y = 0; y < grid.height; y++) {
                    if(grid.get(x,y) == EMPTY)
                        empty.push({x:x, y:y});
                }
            }
            var randomPosition = empty[Math.floor(Math.random()*empty.length)];
            grid.set(FRUIT, randomPosition.x, randomPosition.y);
        }

        //Game Objects, elementi necessari al gioco per funzionare
        //oltre alla griglia e allo snake
        var canvas, ctx, frames, keystate, score = 0, playing = true;

        function main() {
            //Prendo il canvas dall'html del gioco
            canvas = document.getElementById("snake");
            //L'elemento ctx disegnerà all'interno del canvas
            ctx = canvas.getContext("2d");
            //frames tiene il conto delle volte che si è aggiornata
            //l'animazione
            frames = 0;
            //L'oggetto keystate è un oggetto vuoto
            //Aggiungo degli EventListener per le frecce
            //e ad ogni evento aggiungo una chiave che è il
            //valore del pulsante premuto nella tastiera
            //e il suo valore a true
            keystate = {};
            document.addEventListener("keydown", function(evt) {
              if(snake._queue.length > 1)
                switch(snake.direction) {
                  case LEFT: 
                  if(evt.keyCode != RIGHT_CODE)
                    keystate[evt.keyCode] = true;
                  break;
                  case RIGHT:
                  if(evt.keyCode != LEFT_CODE)
                    keystate[evt.keyCode] = true;
                  break;
                  case UP:
                  if(evt.keyCode != DOWN_CODE)
                    keystate[evt.keyCode] = true;
                  break;
                  case DOWN:
                  if(evt.keyCode != UP_CODE)
                    keystate[evt.keyCode] = true;
                  break;
                 default:
                  keystate[evt.keyCode] = true;
                  break;
                  }
              else 
                  keystate[evt.keyCode] = true;
              //Evito lo scroll della window
              switch(evt.keyCode) {
                case LEFT_CODE : case RIGHT_CODE : case UP_CODE : case DOWN_CODE : evt.preventDefault();
                break; default: break;
              }
            });
            document.addEventListener("keyup", function(evt) {
              delete keystate[evt.keyCode];
            });
            //inizializzo il gioco
            init();
            //richiamo il loop di gioco
            loop();

        }
        
        /**
         * Inizializza la griglia di gioco, con serpente e cibo
         * al suo interno
         */
        function init() {
            grid.init(EMPTY, COLS, ROWS);
            var startPosition =   {x:Math.floor(COLS/2), y: COLS-1};
            snake.init(UP, startPosition.x, startPosition.y);
            grid.set(SNAKE, startPosition.x, startPosition.y);
            setFood();
        }
        
        /**
         * Il loop() sarà la funzione di callBack dal metodo
         * requestAnimationFrame, che aggiornerà l'animazione
         * del canvas 
         */
        function loop() {
            update();
            //playing sarà messo a false (e quindi l'animazione finisce)
            //quando c'è il gameOver
            if(playing) {
              draw();
              window.requestAnimationFrame(loop, canvas);
            }
          }


        /**
         * Aggiorna la posizione del serpente ogni "actualSpeed" frames. Ciò
         * determinerà la velocità dello spostamento. Inoltre la direzione e 
         * quindi la posizione si aggiorna in base all'input dell'utente. 
         */
        function update() {
            frames+=1;
            //Utilizzo la variabile actualSpeed per aumentare la velocità
            //Dopo aver raggiunto il punteggio 150 essa aumenta
            var actualSpeed = 5;
            if(score >= 150)
                actualSpeed = 3;
            if(keystate[UP_CODE]) snake.direction = UP;
            if(keystate[DOWN_CODE]) snake.direction = DOWN;
            if(keystate[LEFT_CODE]) snake.direction = LEFT;
            if(keystate[RIGHT_CODE]) snake.direction = RIGHT;
            //Ogni actualSpeed "frame" aggiorno la posizione del serpente
            //Se il punteggio supera 150 la difficoltà aumenta
            if(frames%actualSpeed == 0) {
              frames = 0;
              var nx = snake.last.x;
              var ny = snake.last.y;
              switch(snake.direction) {
                case UP:
                  ny--;
                  break;
                case DOWN:
                  ny++;
                  break;
                case LEFT:
                  nx--;
                  break;
                case RIGHT:
                  nx++;
                  break;
              }
              //controllo le condizioni di gameOver()
              if(nx < 0 || nx > grid.width-1 || ny < 0 || ny > grid.height-1 || grid.get(nx,ny) === SNAKE) {
                document.getElementById("deathSound").play();
                playing = false;
                //return 
                return gameOver();
              }
              //controllo le condizioni di punteggio (si è raggiunto il cibo)
              //sarà inserito un nuovo pezzo del serpente(specificato da tail)
              //se si raggiunge il cibo viene aggiunto
              if(grid.get(nx, ny) == FRUIT) {
                document.getElementById("eatingSound").play();
                var tail = {x:nx, y:ny};
                score +=5;
                setFood();
              } else {
                  //altrimenti viene prima rimosso e poi reinserito
                  //ovvero il serpente resta uguale
                  var tail = snake.remove();
                  grid.set(EMPTY, tail.x, tail.y);
                  tail.x = nx;
                  tail.y = ny;
              }
              snake.insert(tail.x, tail.y);
              grid.set(SNAKE, tail.x, tail.y);
              document.getElementById("snakeScore").innerHTML = "Score = "+score;
            }
        }
        
        /**
         * Il gameOver() rimuove l'elemento canvas (dove avviene il gioco) e lo sostituisce
         * con la scritta gameOver e un bottone per ricominciare la partita. Al click dell'utente
         * sul bottone, viene riaggiunto il canvas in modo che la funzione main() possa operare,
         * e la variabile playing torna a true in modo da far richiamare loop() da requestAnimationFrame()
         */
        function gameOver() {
          //Utilizziamo jQuery per connettersi al server e aggiornare mysql
          $.get("updateScore.php?snake="+score+"", function(data) {
            if(data!="NoError ")
              alert(data);
          });
          //Resetta lo score
          score=0;
          //Rimuove il canvas
          canvas = document.getElementById("snake");
          divGioco = document.getElementsByClassName("game")[0];
          divGioco.removeChild(canvas);
          //Aggiunge il GameOver
          var paragraph = document.createElement("p");
          paragraph.innerHTML = "GAMEOVER";
          paragraph.id = "gameOver";
          divGioco.appendChild(paragraph);
          //Aggiunge il button di restart
          var restartButton = document.createElement("button");
          restartButton.innerHTML = "Restart";
          restartButton.style.float = "center";
          divGioco.appendChild(restartButton);
          //gestisco le condizioni di restart, resetto il div
          restartButton.onclick = function() {
            var br = document.getElementsByTagName("br")[0];
            divGioco.removeChild(paragraph);
            divGioco.removeChild(restartButton);
            divGioco.removeChild(br);
            divGioco.removeChild(exitButton);
            divGioco.appendChild(canvas);
            playing = true;
            main();
          };
          //Aggiunge il button di exit
          divGioco.appendChild(document.createElement("br"));
          var exitButton = document.createElement("button");
          exitButton.style.float = "center";
          exitButton.innerHTML = "Exit";
          divGioco.appendChild(exitButton);
          //Gestisco le condizioni dell'exitButton
           exitButton.onclick = function() {
              window.location.href = "index.php";
            };
        }
        
        /**
         * Disegna il canvas con la griglia, il serpente e il cibo.
         */
        function draw() {
            //Spazio di una casella all'interno della griglia sul canvas
            var elementWidth = canvas.width/grid.width;
            var elementHeight = canvas.height/grid.height;
            for(var x = 0; x < grid.width; x++) {
                for(var y = 0; y < grid.height; y++) {
                    switch(grid.get(x,y)) {
                        case EMPTY:
                            ctx.fillStyle = "black";
                            break;
                        case SNAKE:
                            ctx.fillStyle = "white";
                            break;
                        case FRUIT:
                            ctx.fillStyle = "white";
                            break;
                    }
                    //Il ctx colora il rettangolo 
                    ctx.fillRect(x*elementWidth, y*elementHeight, elementWidth, elementHeight);
                }

            }

        }
        //Richiamo il main al caricamento della pagina
        window.onload = function() {
        main();
        };