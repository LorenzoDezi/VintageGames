			
	
			/* 
		        Lo script Pong.js è utilizzato per realizzare il gioco omonimo.
		        Utilizza un canvas html per la grafica, e window.requestAnimationFrame
		        per le animazioni
      		*/

			//Le variabili necessarie alla realizzazione del gioco
			var canvas, canvasWidth, canvasHeight, ctx, score, playing;

			window.onload = function() {
				main();
			};

			/* 
				La funzione principale che inizializza i vari oggetti
				e richiama l'animazione
			*/
			function main() {
				canvas = document.getElementById("pong");
				score = 0;
				playing = true;
				canvasWidth = canvas.width;
				canvasHeight = canvas.height;
				ctx = canvas.getContext("2d");
				ball.init(canvasWidth/2,canvasHeight/2);
				player.init();
				computer.init(); 
				window.requestAnimationFrame(loop,canvas);
			}

			//la funzione loop viene richiamata per realizzare l'animazione
			function loop(){
				update();
				if(playing) {
					draw();
					window.requestAnimationFrame(loop,canvas);
				}
			}

			//La funzione draw() colora il canvas, quindi lo sfondo, e disegna gli elementi del gioco al suo interno
			function draw() {
				ctx.fillStyle = "black";
				ctx.fillRect(0,0,canvasWidth, canvasHeight);
				player.draw();
				computer.draw();
				ball.draw();
			}

			//definizione della funzione update
			function update() {
				player.update();
				computer.update(ball);
				ball.update(player.paddle, computer.paddle);
				document.getElementById("pongScore").innerHTML = "Score = " + score;
			}


			/*
		         Il gameOver() rimuove l'elemento canvas (dove avviene il gioco) e lo sostituisce
		         con la scritta gameOver e un bottone per ricominciare la partita. Al click dell'utente
		         sul bottone, viene riaggiunto il canvas in modo che la funzione main() possa operare,
		         e la variabile playing torna a true in modo da far richiamare loop() da requestAnimationFrame()
         	*/
			function gameOver() {
				document.getElementById("loseSound").play();
				//Utilizza jquery per connettersi al server e aggiornare il db
				$.get("updateScore.php?pong="+score+"", function(data) {
					if(data != "NoError ")
						alert(data);
				} );
				//Resetta il punteggio e il flag playing
				score=0;
				playing = false;
				//Ritorno il canvas dall'html
		        canvas = document.getElementById("pong");
		        //Lo rimuovo dal div gioco
		        divGioco = document.getElementsByClassName("game")[0];
		        divGioco.removeChild(canvas);
		        var paragraph = document.createElement("p");
		        paragraph.innerHTML = "GAMEOVER";
		        paragraph.id = "gameOver";
		        divGioco.appendChild(paragraph);
		        var restartButton = document.createElement("button");
		        restartButton.innerHTML = "Restart";
		        var br = document.createElement("br");
		        divGioco.appendChild(restartButton);
		        restartButton.onclick = function() {
		        	divGioco.removeChild(paragraph);
		            divGioco.removeChild(restartButton);
		            divGioco.removeChild(br);
		            divGioco.removeChild(exitButton);
		            divGioco.appendChild(canvas);
		            main();
		        };
		        divGioco.appendChild(br);
		        var exitButton = document.createElement("button");
		        exitButton.innerHTML = "Exit";
		        divGioco.appendChild(exitButton);
		        exitButton.onclick = function() {
		        	window.location.href = "index.php";
		        };
        }


			//Il costruttore di Paddle, dato che servono due oggetti di questo tipo
			//uno per il player e l'altro per il computer
			function Paddle(x,y,width, height) {
				this.x = x;
				this.y = y;
				this.width = width;
				this.height = height;
				this.x_speed = 0;
				this.y_speed = 0;
			}
			
			//Sposta il Paddle in posizione x,y. Se arriva ai lati
			//si ferma
			Paddle.prototype.move = function(x) {
				this.x += x;
				this.x_speed = x;
				if(this.x < 0) {
					//tutto a sinistra
					this.x = 0;
					this.x_speed = 0;
				} else if (this.x + this.width > canvasWidth) {
					//tutto a destra
					this.x = canvasWidth - this.width;
					this.x_speed = 0;
				}
			};

			//Disegna il paddle all'interno del canvas
			Paddle.prototype.draw = function() {
				ctx.fillStyle = "white";
				ctx.fillRect(this.x,this.y,this.width,this.height);
			};

			//La variabile keysDown sarà un array a cui spetta il compito
			//di "ascoltare" la pressione dei tasti dell'utente
			var keysDown = {};

			window.addEventListener("keydown", function(event) {
				keysDown[event.keyCode] = true;
			});

			window.addEventListener("keyup", function(event) {
				delete keysDown[event.keyCode];
			});


			//definizioni delle variabili e quindi degli elementi del gioco
			
			//il player incapsula il paddle controllato dal giocatore.
			var player = {

				paddle: null,

				/* 
					Inizializza il paddle nella sua posizione
				*/
				init: function() {
					this.paddle = new Paddle(canvasWidth/2-15,canvasHeight-10,canvasWidth/6,5);
				},
			
				//La funzione update dell'oggetto player sposta il paddle
				//nella direzione specificata dall'utente
				update: function() {
					for(var key in keysDown) {
						var value = Number(key);
						if(value == 37) {
							//freccia sinistra
							this.paddle.move(-4);
						} else if (value == 39) {
							//freccia destra
							this.paddle.move(4);
						} else {
							this.paddle.move(0);
						}
					}
				},

				//La funzione draw dell'oggetto player è un semplice richiamo
				//alla funzione draw() dell'oggetto Paddle associato
				draw: function() {
					this.paddle.draw();
				},


			}

			//l'oggetto computer incapsula il paddle controllato dal pc
			var computer = {
				
				paddle : null,

				//Inizializza il canvas 
				init: function() {
					this.paddle = new Paddle(canvasWidth/2-15, 10, canvasWidth/6,5);
				},
				
				
				//La funzione draw() del Computer disegna semplicemente 
				//il suo paddle.
				draw: function() {
					this.paddle.draw();
				},

				//La funzione update() del Computer è una funzione a cui 
				//bisogna passare l'oggetto ball e questa agirà in base alla sua posizione
				update: function(ball) {
					//aggiusta la posizone della x in base a quella della pallina
					var x_pos = ball.x;
					//grazie a questa operazione, calcoliamo la differenza tra la
					//posizione del paddle e della pallina.
					var diff = -((this.paddle.x + (this.paddle.width/2))-x_pos);
					var speed = 0;
					//Il margine del controllo è di 10, il diametro della pallina
					//la diff assegnata è la velocità di spostamento del paddle
					//assegnare una maggiore velocità significa aumentare la difficoltà
					if(diff < -10) {
						//a sinistra
						speed = -5;
					} else if (diff > 10) {
						//a destra
						speed = 5;
					}
					this.paddle.move(speed);
					//gestione dei limiti di spazio
					if(this.paddle.x < 0) {
						this.paddle.x = 0;
					} else if (this.paddle.x + this.paddle.width > canvasWidth) {
						this.paddle.x = canvasWidth - this.paddle.width;
					}
				}
			};

			//oggetto pallina
			var ball = {

				x : null, y: null, x_speed: null, y_speed: null, radius: null,

				//La funzione init inizializza la pallina con posizione, misure
				//e velocità
				init: function(x,y) {
					this.x = x;
					this.y = y;
					this.x_speed = 0;
					this.y_speed = 3;
					this.radius = 5;
				},

				//La funzione update aggiorna la posizione della pallina
				update: function(paddle1,paddle2) {
					//La speed determina la velocità di spostamento
					//e quindi l'aggiornamento della x
					this.x += this.x_speed;
					this.y += this.y_speed;
					//Si utilizza il 5 per definire i limiti
					//della pallina in quanto il raggio è 5
					var top_x = this.x-5;
					var top_y = this.y-5;
					var bottom_x = this.x+5;
					var bottom_y = this.y+5;
					if(top_x<0) {
						document.getElementById("bounceSound").play();
						//significa che ha urtato il muro sinistro
						this.x = 5;
						//la pallina deve rimbalzare
						this.x_speed = -this.x_speed;
					} else if (bottom_x > canvasWidth) {
						document.getElementById("bounceSound").play();
						//significa che ha urtato il muro destro
						this.x = canvasWidth-5;
						//la pallina deve rimbalzare
						this.x_speed = -this.x_speed;
					}

					//Se siamo dalla parte di campo del player
					if(top_y > canvasHeight/2) {
						if(top_y < (paddle1.y + paddle1.height) && bottom_y > paddle1.y && top_x < (paddle1.x + paddle1.width) && bottom_x > paddle1.x) {
							//ha colpito il paddle del player
							document.getElementById("touchSound").play();
							this.y_speed = -3;
							//la velocità della pallina (e la direzione quindi) sarà determinata dalla direzione dello 
							//spostamento 8del paddle
							this.x_speed += (paddle1.x_speed/2);
							this.y += this.y_speed;
						}
					} else {
						//altrimenti siamo dalla parte del pc
						if(top_y < (paddle2.y + paddle2.height) && bottom_y > paddle2.y && top_x < (paddle2.x + paddle2.width) && bottom_x > paddle2.x) {
							//ha colpito il paddle del computer
							document.getElementById("touchSound").play();
							this.y_speed = 3;
							//la velocità aumenterà in base a quella del paddle, quindi se colpisce il 
							//paddle in movimento. Più volte lo colpisce più aumenta
							this.x_speed += (paddle2.x_speed/2);
							this.y += this.y_speed;
						}
					}
					//Sono state toccate le mete, è stato effettuato un punto o è gameover
					if(this.y < 0) {
						document.getElementById("winSound").play();
						score += 100;
						this.init(canvasWidth/2,canvasHeight/2);
					} else if (this.y>canvasHeight) {
						gameOver();
					}


				},

				//Usa la funzione arc del context per disegnare la circonferenza della pallina
				draw:function() {
					ctx.beginPath();
					ctx.arc(this.x, this.y, this.radius, 2*Math.PI, false);
					ctx.fillStyle = "white";
					ctx.fill();
				}
			};

			

			









