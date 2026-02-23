<style>
   

/* Portfolio Filters Section */
.resource-filters {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding: 15px 0px;
    gap:20px;
    border-radius:5px;
}

.filter-group {
    position: relative;
    flex: 1;
}

.filter-select {
    width: 100%;
    
    border-radius: 5px;
    border: 1px solid #e6e6e6;
    /* box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.03), 0px 3px 6px rgba(0, 0, 0, 0.02); */
    box-shadow: none !important;
    transition: background 0.15s ease, border 0.15s ease, box-shadow 0.15s ease, color 0.15s ease;
    font-size: 14px;
    outline: none;
    height: 100%;
    /* transition: border-color 0.3s; */
    color: #000;
}
.resource-filters .resource-filter-item{
    width:100%;
}

.resource-filters h6.filter-heading svg {top: 1px;left: 5px;position: relative;}

.filter-select:focus {
    border-color: #e6e6e6; /* Highlighted border color on focus */
}

/* Dropdown Icon */
.dropdown-icon {
    position: absolute;
    right: 12px;
    width: 10px;
    top: 44%;
    transform: translateY(-50%);
}
/* Hide the default dropdown arrow */
.filter-select {
    appearance: none; /* Remove default styling */
    -webkit-appearance: none; /* For Safari */
    -moz-appearance: none; /* For Firefox */
    background-image: none; /* Remove any background image */
}

.filter-heading {
    color: #040432 !important;
    font-size: 20px;
}


@media(max-width:768px){
    .resource-filters{
        flex-wrap:wrap;
        gap:0px;
    }
    .filter-heading {
        margin: 15px 0px 10px 0px;
    }
    .resource-grid {
        display: grid;
        gap: 20px;
    }

}

/* Custom styling for the select box if needed */
.filter-select {
    cursor: pointer; /* Change cursor to pointer */
}
/* Portfolio Grid */
.resource-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)) !important;
    gap: 20px;
}
@media(max-width:1024px){
    .resource-grid {
     grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)) !important;
    }
}
@media(max-width:768px){
    .resource-grid {
     grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)) !important;
    }
}
.resource-item {
    background-color: #fff;
    border-radius: 25px;
    padding: 15px;
    text-align:center;
    transition: box-shadow 0.3s;
}

/* Portfolio Item Title */
.resource-item h3 {
    font-size: 18px;
    color:#3263A4;
    text-align:center;
    margin-bottom: 10px;
}
.resource-item h3:hover {
    color:#3263A4;
}
/* Portfolio Item Thumbnail */
.resource-item img {
    width: 100%;
    height: auto;
    border-radius: 15px;
}


/* No Results Message */
.no-results {
    padding: 15px;
    background-color: #ffefef;
    border: 1px solid #f5c6cb;
    color: #721c24;
    border-radius: 5px;
}

/* Load More Button */
.load-more-btn-wrapper {
    text-align: center;
    margin-top: 50px;
}

.vg-btn {
    /* background-color: #0056b3; */
    color: #fff;
    padding: 10px 20px;
    border: none;
    /* border-radius: 4px; */
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.2s;
}

.vg-btn:hover {
    color:#FCE58E;
}

.btn-blue-loader {
    display: none; /* Assume this is a loader for AJAX */
}



.resource-image-link {
    position: relative;
    display: block; 
}

.resource-grid .resource-item:hover .resource-image-link::before {
    content: url(/wp-content/uploads/2021/10/link.png) !important; 
}

.resource-grid .resource-item:hover .resource-image-link::after {
    content: ''; /* Required to have a pseudo-element */
}
.resource-grid .resource-item:hover .resource-image-link::after {
    background: rgba(255, 255, 255, 0.75);
    position: absolute; /* Ensuring it covers the image */
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 1; 
}
.resource-grid .resource-image-link::before {
    transform: scale(0.4);
    width: 1em; /* Icon size */
    display: inline-block; /* Necessary for the icon display */
    position: absolute;
    top: 10%; /* Adjust as necessary */
    right: 0; /* Adjust as necessary */
    left:40%;
    z-index: 2; /* Ensure the icon is always on top */
    line-height: 1em; /* Keeps icon centered vertically */
    transition: transform 0.3s ease; /* Smooth transition on hover */
}

/*** Tooltips */
.tooltip-container {
    position: relative;
    display: inline-block;
    cursor: pointer;
}

.tooltip-text {
    visibility: hidden;
    width: 200px; /* Increase width */
    max-width: 300px; /* Optional: Limit maximum width */
    background-color: #fff;
    color: #040432;
    font-size: 12px;
    text-align: left; /* Align text for better readability */
    padding: 10px;
    border-radius: 6px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.15);
    position: absolute;
    z-index: 10;
    top: 23px;
    left: 105px;
    transform: translateX(-50%);
    opacity: 0;
    transition: opacity 0.3s ease-in-out;
    border: 1px solid #ddd;
    white-space: normal; /* Ensure text wraps properly */
    line-height: 1.4; /* Improve readability */
}

.tooltip-container.active .tooltip-text {
    visibility: visible;
    opacity: 1;
}


</style>