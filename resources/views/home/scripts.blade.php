<script>
document.addEventListener('DOMContentLoaded', function() {
    // Typing animation for hero section
    const line1 = document.getElementById('line1');
    const line2 = document.getElementById('line2');
    
    if (line1 && line2) {
        const cursor = document.createElement('span');
        cursor.className = 'inline-block w-1 h-12 bg-yellow-300 animate-blink ml-1';
        const text1 = line1.dataset.text;
        const text2 = line2.dataset.text;
        let index1 = 0;
        let index2 = 0;
        line1.innerHTML = '';
        line2.innerHTML = '';
        line1.appendChild(cursor);

        function typeLine1() {
            if (index1 < text1.length) {
                line1.innerHTML = text1.substring(0, index1 + 1);
                line1.appendChild(cursor);
                index1++;
                setTimeout(typeLine1, 50);
            } else {
                setTimeout(() => {
                    line1.removeChild(cursor);
                    line2.appendChild(cursor);
                    typeLine2();
                }, 500);
            }
        }

        function typeLine2() {
            if (index2 < text2.length) {
                line2.innerHTML = text2.substring(0, index2 + 1);
                line2.appendChild(cursor);
                index2++;
                setTimeout(typeLine2, 50);
            } else {
                line2.removeChild(cursor);
            }
        }

        typeLine1();
    }
});

</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const animateValue = (obj, start, end, duration) => {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            
            // Power4 Ease Out: starts fast, ends very smooth
            const easeProgress = 1 - Math.pow(1 - progress, 4);
            
            const current = Math.floor(easeProgress * (end - start) + start);
            obj.innerHTML = current.toLocaleString();
            
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = parseInt(entry.target.getAttribute('data-target'));
                animateValue(entry.target, 0, target, 2500); // 2.5 seconds
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.counter-slide').forEach(el => observer.observe(el));
});
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Stats animation for hero section
    const resolvedPercentageEl = document.getElementById('resolved-percentage');
    if (resolvedPercentageEl) {
        const targetPercentage = parseInt(resolvedPercentageEl.dataset.percentage) || 0;
        const animationDuration = 2000; // 2 seconds
        
        // Animate stat counters
        document.querySelectorAll('.stat-counter').forEach(el => {
            const target = parseInt(el.dataset.target) || 0;
            let startTimestamp = null;
            
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / animationDuration, 1);
                
                // Ease out cubic: smooth deceleration
                const easeProgress = 1 - Math.pow(1 - progress, 3);
                
                const current = Math.floor(easeProgress * target);
                el.innerHTML = current.toLocaleString();
                
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        });
        
        // Animate progress circle and percentage text
        const progressCircle = document.querySelector('.stat-progress-circle');
        const percentageText = document.querySelector('.stat-percentage');
        
        if (progressCircle && percentageText) {
            const circumference = 282.7; // stroke-dasharray value
            let startTimestamp = null;
            
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / animationDuration, 1);
                
                // Ease out cubic
                const easeProgress = 1 - Math.pow(1 - progress, 3);
                
                // Calculate stroke-dashoffset: animate from full (282.7) to calculated offset
                const currentPercentage = Math.floor(easeProgress * targetPercentage);
                const offset = circumference * (100 - currentPercentage) / 100;
                
                progressCircle.setAttribute('stroke-dashoffset', offset);
                percentageText.innerHTML = currentPercentage + '%';
                
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }
    }
});
</script>