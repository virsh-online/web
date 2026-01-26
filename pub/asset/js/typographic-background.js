/**
 * 3D Spinning Typographic Background with Gravity
 * Creates randomly spinning typographic symbols in 3D space with physics
 */
(function() {
    'use strict';

    // Configuration
    const config = {
        symbolCount: 30,
        symbols: ['§', '¶', '※', '❧', '⸙', '⁂', '№', '❡', '⌕', '©', '®', '@', '#', '&', '*', '~', '^', '=', '+', '-', '×', '÷', '∞', '∑', '∫', 'α', 'β', 'γ', 'δ', '✣'],
        gravity: 0.15, // Gravity acceleration
        friction: 0.98, // Air resistance
        bounceDamping: 0.7, // Energy loss on bounce
        minSize: 20,
        maxSize: 80,
        rotationSpeed: { min: 0.5, max: 3 },
        opacity: { min: 0.05, max: 0.15 },
        targetFrameTime: 16.67, // 60 FPS = 1000ms/60
        rotationSpeedMultiplier: 0.02, // Controls rotation animation speed
        minVelocityThreshold: 0.1, // Minimum velocity to consider symbol as moving
        bottomAreaThreshold: 0.9, // Bottom 10% of screen
        resetProbability: 0.01, // 1% chance per frame to reset stationary symbols
        maxDeltaTimeMultiplier: 2, // Cap delta time to prevent large jumps
        highlightColor: 'rgba(139, 69, 19, OPACITY)' // CSS --highlight color
    };

    class TypographicSymbol {
        constructor(canvas) {
            this.canvas = canvas;
            this.reset();
        }

        // Helper function to generate random rotation speed
        getRandomRotationSpeed() {
            return (Math.random() - 0.5) * (config.rotationSpeed.max - config.rotationSpeed.min) + config.rotationSpeed.min;
        }

        reset() {
            // Random position
            this.x = Math.random() * this.canvas.width;
            this.y = Math.random() * this.canvas.height * 0.5; // Start in upper half
            
            // Random velocity
            this.vx = (Math.random() - 0.5) * 2;
            this.vy = (Math.random() - 0.5) * 2;
            
            // Random symbol
            this.symbol = config.symbols[Math.floor(Math.random() * config.symbols.length)];
            
            // Random size
            this.size = config.minSize + Math.random() * (config.maxSize - config.minSize);
            
            // 3D rotation angles
            this.rotationX = Math.random() * Math.PI * 2;
            this.rotationY = Math.random() * Math.PI * 2;
            this.rotationZ = Math.random() * Math.PI * 2;
            
            // Random rotation speeds
            this.rotationSpeedX = this.getRandomRotationSpeed();
            this.rotationSpeedY = this.getRandomRotationSpeed();
            this.rotationSpeedZ = this.getRandomRotationSpeed();
            
            // Random opacity
            this.baseOpacity = config.opacity.min + Math.random() * (config.opacity.max - config.opacity.min);
        }

        update(deltaTime) {
            // Apply gravity (acceleration is constant regardless of mass)
            this.vy += config.gravity * deltaTime;
            
            // Apply friction
            this.vx *= config.friction;
            this.vy *= config.friction;
            
            // Update position
            this.x += this.vx * deltaTime;
            this.y += this.vy * deltaTime;
            
            // Update 3D rotations
            this.rotationX += this.rotationSpeedX * deltaTime * config.rotationSpeedMultiplier;
            this.rotationY += this.rotationSpeedY * deltaTime * config.rotationSpeedMultiplier;
            this.rotationZ += this.rotationSpeedZ * deltaTime * config.rotationSpeedMultiplier;
            
            // Boundary collision with bounce
            // Bottom
            if (this.y + this.size / 2 > this.canvas.height) {
                this.y = this.canvas.height - this.size / 2;
                this.vy = -Math.abs(this.vy) * config.bounceDamping;
            }
            
            // Top
            if (this.y - this.size / 2 < 0) {
                this.y = this.size / 2;
                this.vy = Math.abs(this.vy) * config.bounceDamping;
            }
            
            // Right
            if (this.x + this.size / 2 > this.canvas.width) {
                this.x = this.canvas.width - this.size / 2;
                this.vx = -Math.abs(this.vx) * config.bounceDamping;
            }
            
            // Left
            if (this.x - this.size / 2 < 0) {
                this.x = this.size / 2;
                this.vx = Math.abs(this.vx) * config.bounceDamping;
            }
            
            // Reset if symbol is nearly stationary at bottom
            if (Math.abs(this.vy) < config.minVelocityThreshold && this.y > this.canvas.height * config.bottomAreaThreshold) {
                if (Math.random() < config.resetProbability) {
                    this.reset();
                }
            }
        }

        draw(ctx) {
            ctx.save();
            
            // Move to symbol position
            ctx.translate(this.x, this.y);
            
            // Apply 3D rotation effects
            // Using rotation matrices to simulate 3D perspective
            const scaleX = Math.cos(this.rotationY);
            const scaleY = Math.cos(this.rotationX);
            const skewX = Math.sin(this.rotationY) * 0.5;
            
            // Calculate opacity based on rotation (facing camera = more visible)
            const facingFactor = (Math.cos(this.rotationY) + 1) / 2 * (Math.cos(this.rotationX) + 1) / 2;
            const opacity = this.baseOpacity * (0.3 + facingFactor * 0.7);
            
            // Apply transformations
            ctx.transform(scaleX, skewX, -skewX, scaleY, 0, 0);
            ctx.rotate(this.rotationZ);
            
            // Draw symbol
            ctx.font = `${this.size}px 'Special Elite', 'Courier Prime', monospace`;
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillStyle = config.highlightColor.replace('OPACITY', opacity);
            ctx.fillText(this.symbol, 0, 0);
            
            ctx.restore();
        }
    }

    class TypographicBackground {
        constructor() {
            this.canvas = null;
            this.ctx = null;
            this.symbols = [];
            this.lastTime = performance.now();
            this.animationId = null;
            
            this.init();
        }

        init() {
            // Create canvas
            this.canvas = document.createElement('canvas');
            this.canvas.id = 'typographic-background';
            this.canvas.style.position = 'fixed';
            this.canvas.style.top = '0';
            this.canvas.style.left = '0';
            this.canvas.style.width = '100%';
            this.canvas.style.height = '100%';
            this.canvas.style.zIndex = '0';
            this.canvas.style.pointerEvents = 'none';
            
            document.body.insertBefore(this.canvas, document.body.firstChild);
            
            this.ctx = this.canvas.getContext('2d');
            
            // Set canvas size
            this.resize();
            
            // Create symbols
            for (let i = 0; i < config.symbolCount; i++) {
                this.symbols.push(new TypographicSymbol(this.canvas));
            }
            
            // Setup event listeners
            window.addEventListener('resize', () => this.resize());
            
            // Start animation
            this.animate();
        }

        resize() {
            this.canvas.width = window.innerWidth;
            this.canvas.height = window.innerHeight;
        }

        animate() {
            const currentTime = performance.now();
            const deltaTime = Math.min((currentTime - this.lastTime) / config.targetFrameTime, config.maxDeltaTimeMultiplier);
            this.lastTime = currentTime;
            
            // Clear canvas
            this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
            
            // Update and draw symbols
            this.symbols.forEach(symbol => {
                symbol.update(deltaTime);
                symbol.draw(this.ctx);
            });
            
            this.animationId = requestAnimationFrame(() => this.animate());
        }

        destroy() {
            if (this.animationId) {
                cancelAnimationFrame(this.animationId);
            }
            if (this.canvas && this.canvas.parentNode) {
                this.canvas.parentNode.removeChild(this.canvas);
            }
        }
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            new TypographicBackground();
        });
    } else {
        new TypographicBackground();
    }
})();
