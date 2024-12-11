window.addEventListener("scroll", function() {
    const header = document.querySelector(".sticky-header");
    if (window.scrollY > 50) {
      header.classList.add("is-scrolling");
    } else {
      header.classList.remove("is-scrolling");
    }
  });
  

  // Function to generate Google Maps link dynamically
  function generateGoogleMapsLink(placeName) {
    const baseUrl = "https://www.google.com/maps/search/?api=1&query=";
    const encodedPlaceName = encodeURIComponent(placeName);
    return baseUrl + encodedPlaceName;
  }

  // Function to fetch an image from Unsplash
  async function fetchImageForPlace(placeName) {
    const accessKey = '0masAcOBcskRzlAlmhTMZ3Oy-wgVklR5ZwnlQwmczFY';
    const url = `https://api.unsplash.com/search/photos?query=${encodeURIComponent(placeName)}&client_id=${accessKey}&per_page=1`;
    try {
      const response = await fetch(url);
      const data = await response.json();
      if (data.results.length > 0) {
        return data.results[0].urls.regular; // Return the first image URL
      } else {
        return "https://via.placeholder.com/300x200?text=No+Image"; // Fallback if no image found
      }
    } catch (error) {
      console.error("Error fetching image:", error);
      return "https://via.placeholder.com/300x200?text=Error"; // Fallback in case of error
    }
  }

  // Dynamically update the page on load
  window.onload = async function () {
    const tourismPlaces = document.querySelectorAll(".tourism-place");
    for (const place of tourismPlaces) {
      const placeName = place.dataset.name;
      const googleMapsLink = generateGoogleMapsLink(placeName);
      
      // Set Google Maps link
      place.querySelector(".google-maps-link").setAttribute("href", googleMapsLink);

      // Fetch and set the dynamic image
      const img = place.querySelector("img");
      const dynamicImageUrl = await fetchImageForPlace(placeName);
      img.setAttribute("src", dynamicImageUrl);
    }
  };
  // JavaScript to update the Google Maps link based on user input
document.getElementById('location').addEventListener('input', function() {
  const location = document.getElementById('location').value;
  const mapLink = document.getElementById('google-map-link');

  // Update the Google Maps link dynamically
  if (location) {
    mapLink.href = `https://www.google.com/maps/search/?q=${encodeURIComponent(location)}`;
    mapLink.style.display = 'inline-block'; // Show the link when there's input
  } else {
    mapLink.style.display = 'none'; // Hide the link if no location entered
  }
});
