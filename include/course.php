<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Infinite Faculty Slider</title>
    <style>
        /* Basic body styling (Optional) */


        /* --- Control Buttons --- */
        .controls {
            margin-bottom: 1rem;
            display: flex;
            gap: 10px;
            flex-wrap: wrap; /* Allow wrapping on small screens */
            justify-content: center;
        }
        .controls button {
             background-color: #007bff;
             color: white;
             border: none;
             padding: 8px 15px;
             border-radius: 4px;
             cursor: pointer;
             font-size: 0.9rem;
             transition: background-color 0.2s;
        }
        .controls button:hover {
            background-color: #0056b3;
        }
        .controls button.remove-btn {
            background-color: #dc3545; /* Red for remove */
        }
        .controls button.remove-btn:hover {
            background-color: #c82333;
        }

        /* Main container for the slider */
        .pure-slider-container {
            position: relative;
            width: 100%;
            max-width: 1200px; /* Max width */
            margin: auto;
            overflow: hidden; /* Hide overflowing cards */
            border-radius: 0.5rem; /* Rounded corners */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            margin-bottom: 20px; /* Space before the inquire button */
        }

        /* Wrapper for all cards - enables flexbox and transitions */
        .pure-slider-container #slider-wrapper {
            display: flex;
            /* Transition applied via JS for better control during jumps */
            /* transition: transform 0.5s ease-in-out; */
        }

        /* Individual card styling */
        .pure-slider-container .card-item {
            flex-shrink: 0; /* Prevent cards from shrinking */
            width: 100%; /* Default width for mobile */
            padding: 0.5rem; /* Reduced padding around card item */
            box-sizing: border-box; /* Include padding in the element's total width */
            position: relative; /* For remove button positioning */
        }

        /* Inner card structure */
        .pure-slider-container .card {
            border-radius: 0.5rem;
            overflow: hidden;
            height: 400px; /* Fixed height for consistency */
            display: flex;
            flex-direction: column;
            color: #fff; /* Default text color for cards */
            position: relative; /* Needed for overlay absolute positioning */
        }

        /* Card Header */
        .pure-slider-container .card-header {
            padding: 0.6rem 1rem; /* Padding */
            font-weight: bold;
            font-size: 0.9rem; /* Adjusted font size */
            text-align: center;
            z-index: 3; /* Ensure header is above image/overlay */
            position: relative; /* Needed for z-index */
            background-color: inherit; /* Inherit background from parent .card */
            text-transform: uppercase;
        }

        /* Card Image Area */
        .pure-slider-container .card-image-area {
            width: 100%;
            height: 100%; /* Take remaining height */
            position: absolute; /* Position behind header/body */
            top: 0;
            left: 0;
            overflow: hidden; /* Ensure image respects bounds */
        }

         .pure-slider-container .card-image {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Ensure image covers the area */
            display: block;
        }


        /* Card Body - Content overlay */
        .pure-slider-container .card-body {
            margin-top: 200px; /* Push to bottom */

            padding: 1rem;
            position: relative; /* Context for content */
            z-index: 2; /* Above overlay */
            background: linear-gradient(to top, rgba(0, 0, 0, 0.9) 0%, rgba(0, 0, 0, 0.7) 40%, rgba(0, 0, 0, 0) 70%); /* Gradient overlay integrated */
        }

        /* Position for card content (Redundant if overlay is on body)*/
        .pure-slider-container .card-content {
            /* No longer needed if overlay is part of body */
        }

        /* Card text */
        .pure-slider-container .card-text {
            font-size: 0.85rem;
            margin-bottom: 0px;
            line-height: 1.4;
            min-height: 6em; /* Approx 4 lines */
            color:#ffff;
        }

        /* Card button */
        .pure-slider-container .card-button {
            display: inline-block;
            padding: 0.5rem 1rem;
            font-weight: 600;
            font-size: 0.8rem;
            border-radius: 0.25rem;
            transition: background-color 0.3s ease, color 0.3s ease;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid #fff;
            background-color: transparent;
            color: #fff;
            text-align: center;
        }

        .pure-slider-container .card-button:hover {
             background-color: rgba(255, 255, 255, 0.2);
        }

        /* --- Specific card background colors --- */
         .pure-slider-container .card.bg-maroon { background-color: #8B0000; } /* Dark Red / Maroon */
         .pure-slider-container .card.bg-purple { background-color: #6f42c1; } /* Bootstrap purple */
         .pure-slider-container .card.bg-orange { background-color: #fd7e14; } /* Bootstrap orange */
         .pure-slider-container .card.bg-blue { background-color: #0dcaf0; } /* Bootstrap info cyan/blue */
         .pure-slider-container .card.bg-green { background-color: #198754; } /* Bootstrap success green */
         .pure-slider-container .card.bg-teal { background-color: #20c997; } /* Bootstrap teal */
         .pure-slider-container .card.bg-indigo { background-color: #6610f2; } /* Bootstrap indigo */


        /* Hide scrollbar but allow scrolling */
        .pure-slider-container .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .pure-slider-container .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        /* Navigation button styling */
        .pure-slider-container .slider-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(0, 0, 0, 0.4); /* Slightly lighter */
            color: white;
            border: none;
            padding: 0.8rem;
            border-radius: 50%;
            cursor: pointer;
            z-index: 10;
            transition: background-color 0.3s ease;
            line-height: 0;
            width: 45px; /* Smaller buttons */
            height: 35px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .pure-slider-container .slider-btn:hover {
            background-color: rgba(0, 0, 0, 0.7);
        }
        .pure-slider-container .slider-btn:disabled {
             cursor: not-allowed;
             opacity: 0.5;
        }

        .pure-slider-container #prev-btn { left: 0.5rem; } /* Closer to edge */
        .pure-slider-container #next-btn { right: 0.5rem; } /* Closer to edge */

        /* --- Inquire Now Button --- */
       

      

        
        
        /* Hide remove button on clones */
        .card-item[data-is-clone="true"] .remove-card-btn {
            display: none;
        }


        /* Responsive widths using media queries */
        @media (min-width: 576px) { /* sm */
            .pure-slider-container #slider-wrapper .card-item { width: 50%; }
        }
        @media (min-width: 768px) { /* md */
            .pure-slider-container #slider-wrapper .card-item { width: 33.3333%; }
            .pure-slider-container .card { height: 420px; } /* Slightly taller */
        }
        @media (min-width: 992px) { /* lg */
            .pure-slider-container #slider-wrapper .card-item { width: 25%; }
             .pure-slider-container .card { height: 450px; } /* Taller */
             .pure-slider-container .card-text { font-size: 0.82rem; min-height: 7em; } /* Smaller text, more lines */
        }
        @media (min-width: 1200px) { /* xl */
            .pure-slider-container #slider-wrapper .card-item { width: 20%; }
             .pure-slider-container .card-text { font-size: 0.8rem; min-height: 8em;} /* Even smaller text */
        }
    </style>
</head>
<body>

    <div class="controls">
     

    </div>

    <div class="pure-slider-container">
        <div id="slider-container" class="hide-scrollbar">
            <div id="slider-wrapper">
                <div class="card-item">
                    <div class="card bg-maroon">
                        <div class="card-header">BUSINESS</div>
                        <div class="card-image-area">
                            <img src="img/course/test.jpg" class="card-image" alt="Business Professionals">
                        </div>
                        <div class="card-body">
                            <p class="card-text">
                                The place where future business leaders, strategic managers, innovative entrepreneurs are forged via up-to-date knowledge and interdisciplinary thinking.
                            </p>
                            <button class="card-button">MORE</button>
                        </div>
                    </div>
                </div>

                <div class="card-item">
                    <div class="card bg-purple">
                        <div class="card-header">HUMANITIES & SCIENCES</div>
                        <div class="card-image-area">
                            <img src="https://placehold.co/400x450/6f42c1/ffffff?text=Humanities" class="card-image" alt="Student in Lab">
                        </div>
                         <div class="card-body">
                            <p class="card-text">
                                The Faculty of Humanities and Sciences strives to develop professionals in the areas of Education, Science, Mathematics and Nursing.
                            </p>
                            <button class="card-button">MORE</button>
                         </div>
                    </div>
                </div>


                <div class="card-item">
                    <div class="card bg-blue">
                        <div class="card-header">ENGINEERING</div>
                        <div class="card-image-area">
                            <img src="https://placehold.co/400x450/0dcaf0/ffffff?text=Engineering" class="card-image" alt="Engineering students">
                        </div>
                         <div class="card-body">
                            <p class="card-text">
                                The faculty offers accredited degree programmes in a wide range of engineering disciplines.
                            </p>
                            <button class="card-button">MORE</button>
                         </div>
                    </div>
                </div>


                <div class="card-item">
                    <div class="card bg-teal">
                        <div class="card-header">COMPUTING</div>
                        <div class="card-image-area">
                            <img src="https://placehold.co/400x450/20c997/ffffff?text=Computing" class="card-image" alt="Coding on screen">
                        </div>
                         <div class="card-body">
                            <p class="card-text">
                                The Faculty of Computing is a pioneering faculty in the field of IT education.
                            </p>
                            <button class="card-button">MORE</button>
                         </div>
                    </div>
                </div>
                 <div class="card-item">
                     <div class="card bg-orange">
                         <div class="card-header">GRADUATE STUDIES</div>
                          <div class="card-image-area">
                             <img src="https://placehold.co/400x450/fd7e14/ffffff?text=Graduate" class="card-image" alt="Graduate Students">
                         </div>
                         <div class="card-body">
                            <p class="card-text">
                                Promoting research and providing study programs that are leading to advanced learning, research and scholarship both within and outside the domain of the country.
                            </p>
                            <button class="card-button">MORE</button>
                         </div>
                     </div>
                 </div>

               
                 

                 <div class="card-item">
                    <div class="card bg-indigo">
                        <div class="card-header">ARCHITECTURE</div>
                         <div class="card-image-area">
                             <img src="https://placehold.co/400x450/6610f2/ffffff?text=Architecture" class="card-image" alt="Architecture Student/Building">
                         </div>
                         <div class="card-body">
                            <p class="card-text">
                                This professional pathway is ideal for creative people with sensitivity, imagination and ability to appreciate the technical aspects of Building Construction.
                            </p>
                            <button class="card-button">MORE</button>
                         </div>
                    </div>
                </div>



                <div class="card-item">
                    <div class="card bg-indigo">
                        <div class="card-header">ghghg</div>
                         <div class="card-image-area">
                             <img src="https://placehold.co/400x450/6610f2/ffffff?text=Architecture" class="card-image" alt="Architecture Student/Building">
                         </div>
                         <div class="card-body">
                            <p class="card-text">
                                This professional pathway is ideal for creative people with sensitivity, imagination and ability to appreciate the technical aspects of Building Construction.
                            </p>
                            <button class="card-button">MORE</button>
                         </div>
                    </div>
                </div>



                <div class="card-item">
                    <div class="card bg-indigo">
                        <div class="card-header">erere</div>
                         <div class="card-image-area">
                             <img src="https://placehold.co/400x450/6610f2/ffffff?text=Architecture" class="card-image" alt="Architecture Student/Building">
                         </div>
                         <div class="card-body">
                            <p class="card-text">
                                This professional pathway is ideal for creative people with sensitivity, imagination and ability to appreciate the technical aspects of Building Construction.
                            </p>
                            <button class="card-button">MORE</button>
                         </div>
                    </div>
                </div>

                 <div class="card-item">
                     <div class="card bg-green">
                         <div class="card-header">HOSPITALITY & CULINARY</div>
                          <div class="card-image-area">
                             <img src="https://placehold.co/400x450/198754/ffffff?text=Hospitality" class="card-image" alt="Chef/Hospitality Staff">
                         </div>
                          <div class="card-body">
                            <p class="card-text">
                                The William Angliss Institute @SLIIT is a joint venture between William Angliss Institute, Melbourne and is based in the SLIIT campus, Malabe.
                            </p>
                            <button class="card-button">MORE</button>
                         </div>
                     </div>
                 </div>
                 </div>
             </div>
         <button id="prev-btn" class="slider-btn" title="Previous">&#9664;</button>
         <button id="next-btn" class="slider-btn" title="Next">&#9654;</button>

    </div>
    


    <script>
        const sliderContainer = document.querySelector('.pure-slider-container');
         if (sliderContainer) {
            const sliderWrapper = sliderContainer.querySelector('#slider-wrapper');
            const prevBtn = sliderContainer.querySelector('#prev-btn');
            const nextBtn = sliderContainer.querySelector('#next-btn');
            const addStartBtn = document.getElementById('add-start-btn');
            const addEndBtn = document.getElementById('add-end-btn');
            const removeFirstBtn = document.getElementById('remove-first-btn');
            const removeLastBtn = document.getElementById('remove-last-btn');

            let cardWidth = 0;
            let currentIndex = 0;
            let cardsToClone = 0;
            let originalCardCount = 0;
            let isTransitioning = false;
            let visibleCards = 1; // Default

            const getVisibleCardsCount = () => {
                // Use a reliable way to get the card item's style, even if the first one is a clone initially
                const firstOriginalCard = sliderWrapper.querySelector('.card-item:not([data-is-clone="true"])');
                 if (!firstOriginalCard) {
                    // If no original cards, try getting any card item - this might happen during removal
                    const anyCard = sliderWrapper.querySelector('.card-item');
                    if (!anyCard) return 1; // Absolute fallback
                     const cardItemStyle = window.getComputedStyle(anyCard);
                     const scrollContainerWidth = parseFloat(window.getComputedStyle(sliderWrapper.parentElement).width);
                     const cardWidthPx = parseFloat(cardItemStyle.width);
                     if (scrollContainerWidth === 0 || cardWidthPx === 0) return 1;
                     return Math.max(1, Math.floor(scrollContainerWidth / cardWidthPx));
                 } else {
                    // Preferred method: use an original card
                    const cardItemStyle = window.getComputedStyle(firstOriginalCard);
                    const scrollContainerWidth = parseFloat(window.getComputedStyle(sliderWrapper.parentElement).width);
                     if (scrollContainerWidth === 0 || !cardItemStyle.width || cardItemStyle.width === 'auto') return 1; // Fallback
                     const cardWidthPercentMatch = cardItemStyle.width.match(/(\d+(\.\d+)?)%/);

                     if (cardWidthPercentMatch) {
                         const cardWidthPercent = parseFloat(cardWidthPercentMatch[1]);
                         if (cardWidthPercent === 0) return 1;
                         // Calculate how many fit based on percentage
                         // Use a small tolerance (e.g., 99.9) to handle potential floating point inaccuracies
                         return Math.max(1, Math.floor(99.9 / cardWidthPercent));
                     } else {
                          // Fallback if width is not in percent (e.g., fixed px width)
                         const cardWidthPx = parseFloat(cardItemStyle.width);
                         if (cardWidthPx === 0) return 1;
                         return Math.max(1, Math.floor(scrollContainerWidth / cardWidthPx));
                     }
                 }
            };

            const updateSliderPosition = (animate = true) => {
                if (!sliderWrapper || cardWidth === 0) return; // Guard clause
                sliderWrapper.style.transition = animate ? 'transform 0.5s ease-in-out' : 'none';
                sliderWrapper.style.transform = `translateX(${-cardWidth * currentIndex}px)`;
            };

            const handleTransitionEnd = () => {
                 isTransitioning = false; // Allow next move

                 // Check if we landed on a clone and need to jump
                 let adjusted = false;
                 if (currentIndex >= originalCardCount + cardsToClone) {
                    // Went past the end (landed on end-clones) -> jump to beginning originals
                    currentIndex = cardsToClone + (currentIndex - (originalCardCount + cardsToClone)); // Adjust index based on how far past the end we went
                    adjusted = true;
                 } else if (currentIndex < cardsToClone) {
                    // Went past the beginning (landed on start-clones) -> jump to end originals
                     currentIndex = originalCardCount + currentIndex; // Map the clone index to the corresponding original index
                    adjusted = true;
                 }

                 if (adjusted) {
                    updateSliderPosition(false); // Jump without animation
                 }
            };

            const moveSlider = (direction) => {
                 // Check if there's only one visible group or fewer cards than visible
                 if (isTransitioning || originalCardCount <= visibleCards) {
                     // console.log("Movement blocked: Transitioning or not enough cards.", {isTransitioning, originalCardCount, visibleCards});
                     return; // Don't move if busy or not enough cards to slide
                 }
                 isTransitioning = true;

                 if (direction === 'next') {
                    currentIndex++;
                 } else { // direction === 'prev'
                    currentIndex--;
                 }
                 updateSliderPosition(true); // Move with animation
            };

             // --- Core Initialization Function ---
             const initializeSlider = () => {
                 console.log("Initializing slider...");
                 isTransitioning = false; // Reset transition lock

                 // 1. Remove existing clones
                 const clones = sliderWrapper.querySelectorAll('.card-item[data-is-clone="true"]');
                 clones.forEach(clone => clone.remove());
                 // console.log(`Removed ${clones.length} old clones.`);

                 // 2. Get the current *original* cards
                 const originalCards = sliderWrapper.querySelectorAll('.card-item:not([data-is-clone="true"])');
                 originalCardCount = originalCards.length;
                 console.log(`Found ${originalCardCount} original cards.`);

                 // 3. Handle state if not enough cards
                 if (originalCardCount === 0) {
                     console.log("No cards found. Disabling slider.");
                     sliderWrapper.style.transform = 'translateX(0px)'; // Reset position
                     prevBtn.disabled = true;
                     nextBtn.disabled = true;
                     // Optionally hide the container or show a message
                      // Re-attach remove listeners to potentially re-added cards (though there are none currently)
                     attachRemoveButtonListeners();
                     return; // Stop initialization
                 }

                 // Calculate how many cards are visible based on current layout/viewport
                 visibleCards = getVisibleCardsCount();
                 console.log(`Visible cards: ${visibleCards}`);


                 // 4. Determine how many cards to clone (at least 1, max needed for smooth wrap)
                 // We need enough clones to fill the viewport on both sides.
                 // Cloning `visibleCards` seems sufficient for most cases for a smooth transition.
                 // If originalCardCount is small, clone fewer, but at least one if sliding is possible.
                  cardsToClone = (originalCardCount > visibleCards) ? Math.max(1, visibleCards) : 0;
                 console.log(`Cloning ${cardsToClone} cards from each end.`);


                 // 5. Clone cards for infinite loop (only if needed)
                 if (cardsToClone > 0) {
                    prevBtn.disabled = false;
                    nextBtn.disabled = false;

                     const firstClones = [];
                     const lastClones = [];

                     for (let i = 0; i < cardsToClone; i++) {
                        // Clone from start to append at end
                        if (originalCards[i]) {
                            const clone = originalCards[i].cloneNode(true);
                            clone.setAttribute('data-is-clone', 'true');
                            firstClones.push(clone);
                        }
                        // Clone from end to prepend at start (handle potential index out of bounds)
                        let endIndex = originalCardCount - 1 - i;
                        if (endIndex >= 0 && originalCards[endIndex]) {
                             const clone = originalCards[endIndex].cloneNode(true);
                             clone.setAttribute('data-is-clone', 'true');
                             lastClones.push(clone);
                        }
                     }

                     // Add clones to the DOM
                     firstClones.forEach(clone => sliderWrapper.appendChild(clone));
                     lastClones.reverse().forEach(clone => sliderWrapper.prepend(clone));
                     // console.log(`Added ${firstClones.length + lastClones.length} new clones.`);
                 } else {
                      // Not enough cards to slide, disable buttons
                     console.log("Not enough cards to slide for infinite loop. Disabling nav buttons.");
                     prevBtn.disabled = true;
                     nextBtn.disabled = true;
                 }


                // 6. Calculate card width (do this *after* potential clones are added and layout might settle)
                 const allCards = sliderWrapper.querySelectorAll('.card-item');
                 if (allCards.length > 0) {
                    // Use offsetWidth for accurate pixel width including padding/border
                    cardWidth = allCards[0].offsetWidth;
                     // console.log(`Calculated card width: ${cardWidth}px`);
                 } else {
                     cardWidth = 0; // Should not happen if initial check passed
                     console.error("Could not find cards to calculate width.");
                 }


                // 7. Reset index and position
                // Start position should show the first *original* card.
                // Originals start after the prepended clones.
                currentIndex = cardsToClone;
                // console.log(`Setting initial index to: ${currentIndex}`);
                updateSliderPosition(false); // Set initial position without animation

                // 8. (Re)Attach transitionend listener (ensure only one is active)
                sliderWrapper.removeEventListener('transitionend', handleTransitionEnd); // Remove previous if exists
                 if (cardsToClone > 0) { // Only add listener if infinite loop is active
                    sliderWrapper.addEventListener('transitionend', handleTransitionEnd);
                 }

                // 9. Attach remove button listeners to all *original* cards (clones don't need them as they are removed and re-added)
                 attachRemoveButtonListeners();


                 console.log("Slider initialization complete.");
             };

            // --- Add/Remove Card Functions ---
            let newCardCounter = 0;
            const createNewCardHtml = () => {
                 newCardCounter++;
                 const colors = ['teal', 'indigo', 'maroon', 'purple', 'orange', 'blue', 'green'];
                 const randomColor = colors[Math.floor(Math.random() * colors.length)];
                 // Simple unique ID for demo purposes
                 const cardId = `card-${Date.now()}-${newCardCounter}`;

                 return `
                  <div class="card-item" data-id="${cardId}">
                    <div class="card bg-${randomColor}">
                         <button class="remove-card-btn" title="Remove this card">X</button>
                         <div class="card-header">NEW CARD ${newCardCounter}</div>
                         <div class="card-image-area">
                            <img src="https://placehold.co/400x450/${randomColor.substring(3)}/ffffff?text=New+${newCardCounter}" class="card-image" alt="Newly Added Card ${newCardCounter}">
                         </div>
                         <div class="card-body">
                            <p class="card-text">
                                This is dynamically added content for card number ${newCardCounter}. Explore more features!
                            </p>
                            <button class="card-button">MORE</button>
                         </div>
                    </div>
                 </div>
                 `;
             };

            const addCard = (position = 'end') => {
                 const newCardHtml = createNewCardHtml();
                 const tempDiv = document.createElement('div');
                 tempDiv.innerHTML = newCardHtml.trim();
                 const newCardElement = tempDiv.firstChild;

                 // Temporarily disable transitions for smoother addition before re-init
                 sliderWrapper.style.transition = 'none';

                 if (position === 'start') {
                    // Find the first *original* card and insert before it
                    const firstOriginal = sliderWrapper.querySelector('.card-item:not([data-is-clone="true"])');
                    if (firstOriginal) {
                        sliderWrapper.insertBefore(newCardElement, firstOriginal);
                    } else {
                         // If no originals (slider was empty), just append
                         sliderWrapper.appendChild(newCardElement);
                    }
                     // If adding to start, the logical index of the previous first card shifts by 1.
                     // So, we need to increment the currentIndex to stay focused on the "same" content.
                     currentIndex++;

                 } else { // 'end'
                    // Find the last *original* card and insert after it
                    const originalCards = sliderWrapper.querySelectorAll('.card-item:not([data-is-clone="true"])');
                     if (originalCards.length > 0) {
                        originalCards[originalCards.length - 1].insertAdjacentElement('afterend', newCardElement);
                     } else {
                        // If no originals, just append
                         sliderWrapper.appendChild(newCardElement);
                     }
                 }
                 console.log(`Added card to ${position}`);
                 // Re-initialize the slider after adding the card
                 initializeSlider();
             };

            const removeCard = (target) => {
                 let cardToRemove = null;
                 const originalCards = sliderWrapper.querySelectorAll('.card-item:not([data-is-clone="true"])');
                 originalCardCount = originalCards.length; // Update count before potential removal

                 if (originalCardCount === 0) {
                     console.log("No cards to remove.");
                     return; // Cannot remove if there are no original cards
                 }

                 let removedOriginalIndex = -1; // Index of the original card that was removed

                 if (target instanceof HTMLElement) {
                    // If the element itself was passed (e.g., from button click)
                    cardToRemove = target.closest('.card-item');
                     // Find its index among original cards
                    if (cardToRemove && !cardToRemove.hasAttribute('data-is-clone')) {
                         removedOriginalIndex = Array.from(originalCards).indexOf(cardToRemove);
                    } else {
                         console.log("Target is not an original card or was not found.");
                         return; // Can only remove original cards this way
                    }
                 } else if (target === 'first') {
                    cardToRemove = originalCards[0];
                     removedOriginalIndex = 0;
                 } else if (target === 'last') {
                    cardToRemove = originalCards[originalCardCount - 1];
                     removedOriginalIndex = originalCardCount - 1;
                 } else {
                    console.log("Invalid target for removeCard.");
                    return;
                 }

                 if (cardToRemove) {
                    console.log(`Removing card: ${cardToRemove.querySelector('.card-header')?.textContent || 'Unnamed Card'}`);

                    // Temporarily disable transitions for smoother removal before re-init
                    sliderWrapper.style.transition = 'none';

                    cardToRemove.remove();

                    // Adjust currentIndex if the removed card was before the current view
                     if (removedOriginalIndex !== -1 && removedOriginalIndex < currentIndex - cardsToClone) {
                         // The removed card was one of the originals that came *before* the currently viewed original.
                         // This means the remaining originals have shifted left by one position relative to the start of the wrapper.
                         // To keep the same content visually centered after re-init, we need to decrement the currentIndex.
                        currentIndex--;
                        // Ensure currentIndex doesn't drop below the new number of start clones
                        if (currentIndex < Math.min(originalCardCount - 1, Math.max(0, visibleCards))) {
                             currentIndex = Math.min(originalCardCount - 1, Math.max(0, visibleCards));
                        }
                        console.log(`Adjusting index after removal: ${currentIndex}`);
                     } else if (removedOriginalIndex === 0 && originalCardCount === 1) {
                         // Special case: Removing the only original card
                         currentIndex = 0; // Reset index
                          console.log("Removed last card, resetting index.");
                     }


                    // Re-initialize the slider after removing the card
                    initializeSlider();

                     // If we removed the last card, the slider might become empty or have only clones.
                     // initializeSlider will handle disabling buttons, but we might need to ensure
                     // the position is correct if it became empty.
                     if (sliderWrapper.querySelectorAll('.card-item:not([data-is-clone="true"])').length === 0) {
                         sliderWrapper.style.transform = 'translateX(0px)';
                     }

                 } else {
                    console.log("Card element not found for removal.");
                 }
            };


             // --- Event Listeners ---

            // Navigation Buttons
            if (prevBtn) {
                prevBtn.addEventListener('click', () => moveSlider('prev'));
            }
            if (nextBtn) {
                nextBtn.addEventListener('click', () => moveSlider('next'));
            }

            // Control Buttons
             if (addStartBtn) {
                 addStartBtn.addEventListener('click', () => addCard('start'));
             }
             if (addEndBtn) {
                 addEndBtn.addEventListener('click', () => addCard('end'));
             }
             if (removeFirstBtn) {
                 removeFirstBtn.addEventListener('click', () => removeCard('first'));
             }
             if (removeLastBtn) {
                 removeLastBtn.addEventListener('click', () => removeCard('last'));
             }

             // Event delegation for remove buttons on cards
             // Attach listener to the sliderWrapper and check if the click target is a remove button
             if (sliderWrapper) {
                 sliderWrapper.addEventListener('click', (event) => {
                     const removeBtn = event.target.closest('.remove-card-btn');
                     if (removeBtn) {
                         const cardItem = removeBtn.closest('.card-item');
                         if (cardItem && !cardItem.hasAttribute('data-is-clone')) {
                             // Only remove original cards via the button on the card
                             removeCard(cardItem);
                         }
                     }
                 });
             }


            // Handle window resize
            const handleResize = () => {
                 // Recalculate visible cards and potentially re-initialize if the number changes
                 const newVisibleCards = getVisibleCardsCount();
                 if (newVisibleCards !== visibleCards || cardWidth !== sliderWrapper.querySelector('.card-item')?.offsetWidth) {
                    console.log("Window resized or visible cards changed. Re-initializing slider.");
                    initializeSlider();
                 } else {
                      // If visible cards count didn't change, just update position in case width changed slightly
                     cardWidth = sliderWrapper.querySelector('.card-item')?.offsetWidth || 0;
                     updateSliderPosition(false); // Snap to current index based on new width
                 }
            };

             let resizeTimeout;
            window.addEventListener('resize', () => {
                 clearTimeout(resizeTimeout);
                 resizeTimeout = setTimeout(handleResize, 100); // Debounce resize
            });


            // --- Initial Setup ---
            initializeSlider(); // Setup the slider on page load

            // Initial check for button states (can be improved within initializeSlider)
            // If originalCardCount is <= visibleCards after init, buttons should be disabled
             const initialOriginalCards = sliderWrapper.querySelectorAll('.card-item:not([data-is-clone="true"])');
             if (initialOriginalCards.length <= getVisibleCardsCount()) {
                 prevBtn.disabled = true;
                 nextBtn.disabled = true;
             }

            // Helper to attach remove listeners (called in initializeSlider)
             function attachRemoveButtonListeners() {
                 // This function is now primarily for clarity, as the main click handling is via delegation
                 // on sliderWrapper. However, you could use this if you prefer attaching directly
                 // to each *original* card, but delegation is more efficient for dynamic elements.
                 // We keep it here as a concept, but the actual removal logic is triggered by the delegated listener.
                 // The delegation listener added to sliderWrapper handles clicks on dynamically added buttons.
             }

         } else {
             console.error("Slider container not found.");
         }

    </script>

</body>
</html>