const express = require("express")
const Item = require("./models/Item")
const router = express.Router()
const queryVar = require('./search/search_query')
const pageLimit = 25


// List all torrents
router.get("/", async (req, res) => {
    const torrents = await Item
        .find()
    .limit(pageLimit)
        .sort('-dt')
    .paginate()
        .exec()
    res.status(200).send(torrents)
})

// Get torrent
router.get("/ext_id/:ext_id", async (req, res) => {
    const torrent = await Item
        .find({ ext_id: req.params.ext_id })
        .exec()
    res.status(200).send(torrent)
})

// Search by title
router.get("/search/:search_title", async (req, res) => {
    let searchQuery = queryVar(req.params.search_title)
    const result = await Item
        .find(searchQuery)
    .limit(pageLimit)
    .paginate()
        .exec()
    res.status(200).send(result)
})

// List all categories
router.get("/categories", async (req, res) => {
    const categories = await Item
        .find()
        .distinct('cat')
        .exec()
    res.status(200).send(categories)
})

// List torrents from specific categories
router.get("/categories/:cat", async (req, res) => {
  const categories = await Item.find().distinct("cat").exec()
  if (!categories.includes(req.params.cat)) {
    return res.status(404).json({ error: "Category not found." })
  }
  const torrents = await Item
    .find({ cat: req.params.cat })
    .limit(pageLimit)
        .sort('-dt')
    .paginate()
        .exec()
    res.status(200).send(torrents)
})


module.exports = router