export default function highlight(elements) {
    let removeTransitionClass = false;
    const highlightClass = '!bg-warning-100';
    const highlightDuration = 2000;

    if(!Array.isArray(elements)) {
        elements = [elements];
    }

    elements.forEach((element) => {
        if (element) {
            if (element.id == 'collapse') {
                // This is so that it only highlight the header as opposed to the entire collapse element
                element = element.querySelector('#collapse-header');
            }

            if (element.classList) {
                if (
                    !element.classList.contains('transition', 'duration-1000')
                ) {
                    removeTransitionClass = true;
                    element.classList.add('transition', 'duration-1000');
                }

                element.classList.add(highlightClass);

                setTimeout(() => {
                    const transitionDuration =
                        parseFloat(
                            window
                                .getComputedStyle(element)
                                .getPropertyValue('transition-duration')
                                .replace('s', '')
                        ) * 1000;
                    element.classList.remove(highlightClass);
                    if (removeTransitionClass) {
                        setTimeout(() => {
                            element.classList.remove(
                                'transition',
                                'duration-1000'
                            );
                        }, transitionDuration);
                    }
                }, highlightDuration);
            }
        }
    });
}
