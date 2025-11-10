document.addEventListener('DOMContentLoaded', function() {
    const sliders = document.querySelectorAll('.archi-image-comparison-slider');
    
    sliders.forEach(slider => {
        const beforeImage = slider.querySelector('.archi-comparison-before');
        const afterImage = slider.querySelector('.archi-comparison-after');
        const orientation = slider.dataset.orientation || 'vertical';
        const initialPosition = parseInt(slider.dataset.initialPosition) || 50;
        
        // Create slider handle
        const handle = document.createElement('div');
        handle.className = 'archi-comparison-handle';
        handle.innerHTML = `
            <div class="archi-handle-line"></div>
            <div class="archi-handle-circle">
                <span class="archi-arrow-left"></span>
                <span class="archi-arrow-right"></span>
            </div>
        `;
        slider.appendChild(handle);
        
        // Initialize position
        updateSliderPosition(slider, initialPosition, orientation);
        
        // Add event listeners
        let isDragging = false;
        
        handle.addEventListener('mousedown', startDrag);
        handle.addEventListener('touchstart', startDrag);
        
        function startDrag(e) {
            isDragging = true;
            e.preventDefault();
        }
        
        document.addEventListener('mousemove', drag);
        document.addEventListener('touchmove', drag);
        
        document.addEventListener('mouseup', stopDrag);
        document.addEventListener('touchend', stopDrag);
        
        function drag(e) {
            if (!isDragging) return;
            
            const rect = slider.getBoundingClientRect();
            let position;
            
            if (orientation === 'horizontal') {
                const y = (e.type.includes('touch') ? e.touches[0].clientY : e.clientY) - rect.top;
                position = (y / rect.height) * 100;
            } else {
                const x = (e.type.includes('touch') ? e.touches[0].clientX : e.clientX) - rect.left;
                position = (x / rect.width) * 100;
            }
            
            position = Math.max(0, Math.min(100, position));
            updateSliderPosition(slider, position, orientation);
        }
        
        function stopDrag() {
            isDragging = false;
        }
    });
});

function updateSliderPosition(slider, position, orientation) {
    const afterImage = slider.querySelector('.archi-comparison-after');
    const handle = slider.querySelector('.archi-comparison-handle');
    
    if (orientation === 'horizontal') {
        afterImage.style.clipPath = `inset(${position}% 0 0 0)`;
        handle.style.top = `${position}%`;
        handle.style.left = '50%';
    } else {
        afterImage.style.clipPath = `inset(0 0 0 ${position}%)`;
        handle.style.left = `${position}%`;
        handle.style.top = '50%';
    }
}
